import { query, mutation } from "./_generated/server";
import { v } from "convex/values";

// 1. Generate a temporary upload URL for local PNG/JPG file uploads
export const generateUploadUrl = mutation(async (ctx) => {
  return await ctx.storage.generateUploadUrl();
});

// 2. Landing Page Query: Fetches top 6 most sought-after products + resolves Convex storage URLs
export const getFeaturedProducts = query({
  handler: async (ctx) => {
    const products = await ctx.db
      .query("products")
      .withIndex("by_sales")
      .order("desc")
      .take(6);

    return await Promise.all(
      products.map(async (product) => ({
        ...product,
        image: product.image.startsWith("http")
          ? product.image
          : (await ctx.storage.getUrl(product.image as any)) ?? product.image,
      }))
    );
  },
});

// 3. Store Page Query: Groups products by category + resolves Convex storage URLs
export const getStoreMenu = query({
  handler: async (ctx) => {
    const allProducts = await ctx.db.query("products").collect();

    const productsWithUrls = await Promise.all(
      allProducts.map(async (product) => ({
        ...product,
        image: product.image.startsWith("http")
          ? product.image
          : (await ctx.storage.getUrl(product.image as any)) ?? product.image,
      }))
    );

    return productsWithUrls.reduce((acc, product) => {
      const cat = product.category;
      if (!acc[cat]) acc[cat] = [];
      acc[cat].push(product);
      return acc;
    }, {} as Record<string, typeof productsWithUrls>);
  },
});

// 4. Mutation: Inserts a new product and initializes salesCount for indexing
export const addProduct = mutation({
  args: {
    name: v.string(),
    price: v.number(),
    category: v.string(),
    image: v.string(),
    description: v.string(),
    stockQuantity: v.number(),
    salesCount: v.optional(v.number()),
  },
  handler: async (ctx, args) => {
    if (args.price < 0) {
      throw new Error("Product price cannot be less than 0.");
    }

    return await ctx.db.insert("products", {
      ...args,
      salesCount: args.salesCount ?? 0,
    });
  },
});

// 5. Mutation: Updates an existing product in-place (prevents duplicates)
export const updateProduct = mutation({
  args: {
    id: v.id("products"),
    name: v.string(),
    price: v.number(),
    category: v.string(),
    image: v.optional(v.string()), // Only provided if user uploaded a new image
    description: v.string(),
  },
  handler: async (ctx, args) => {
    if (args.price < 0) {
      throw new Error("Product price cannot be less than 0.");
    }

    const existing = await ctx.db.get(args.id);
    if (!existing) {
      throw new Error("Product not found.");
    }

    // If a new image was uploaded, clean up the old file in Convex Storage
    if (args.image && existing.image && !existing.image.startsWith("http")) {
      try {
        await ctx.storage.delete(existing.image as any);
      } catch (_) {}
    }

    await ctx.db.patch(args.id, {
      name: args.name,
      price: args.price,
      category: args.category,
      description: args.description,
      ...(args.image ? { image: args.image } : {}),
    });
  },
});

// 6. Mutation: Deletes a product and its stored image in real time
export const deleteProduct = mutation({
  args: {
    id: v.id("products"),
  },
  handler: async (ctx, args) => {
    const existing = await ctx.db.get(args.id);
    if (!existing) return;

    if (existing.image && !existing.image.startsWith("http")) {
      try {
        await ctx.storage.delete(existing.image as any);
      } catch (_) {}
    }

    await ctx.db.delete(args.id);
  },
});