import { Schema, model, connect } from "mongoose";
import { ICategory } from "../interfaces/category.interface";

const schema = new Schema<ICategory>({
  name: { type: String, required: true },
  color: { type: String },
});
export const Category = model<ICategory>("Category", schema);
