import { defineSchema, defineTable } from "convex/server";
import { v } from "convex/values";

export default defineSchema({
  products: defineTable({
    name: v.string(),
    price: v.number(),
    category: v.string(),
    image: v.string(),
    description: v.string(),
    stockQuantity: v.number(),
    salesCount: v.optional(v.number()), // Metric to rank popularity
  })
  .index("by_category", ["category"])
  .index("by_sales", ["salesCount"]),  // Required for fast ordering
});