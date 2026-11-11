import { query, mutation } from "./_generated/server";
import { v } from "convex/values";

// Query for the Landing Page: Fetches top 6 most sought-after products by sales volume
export const getFeaturedProducts = query({
  handler: async (ctx) => {
    return await ctx.db
      .query("products")
      .withIndex("by_sales")
      .order("desc") // Highest sales first
      .take(6);      // Top 6 items to fit a balanced grid (3 columns x 2 rows)
  },
});

// Query for the Store Page: Groups products by category
export const getStoreMenu = query({
  handler: async (ctx) => {
    const allProducts = await ctx.db.query("products").collect();
    return allProducts.reduce((acc, product) => {
      const cat = product.category;
      if (!acc[cat]) acc[cat] = [];
      acc[cat].push(product);
      return acc;
    }, {} as Record<string, typeof allProducts>);
  },
});

// Mutation: Add new product
export const addProduct = mutation({
  args: {
    name: v.string(),
    price: v.number(),
    category: v.string(),
    image: v.string(),
    description: v.string(),
    stockQuantity: v.number(),
  },
  handler: async (ctx, args) => {
    return await ctx.db.insert("products", args);
  },
});