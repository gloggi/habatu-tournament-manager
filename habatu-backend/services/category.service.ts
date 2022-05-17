import { Category } from "../models";
import { ICategory } from '../interfaces/category.interface';
import { Types } from "mongoose";

export const createCategory = async (category: ICategory) => {
    return await Category.create(category)
}
export const getCategory = async (_id: string) => {
    if (!Types.ObjectId.isValid(_id)) {
        throw new Error("")
    }
    const category = await Category.findOne({ _id });
    return category
}
export const getCategorys = async () => {
    const categorys = await Category.find({});
    return categorys
}

export const updateCategorys = async (_id: String, newCategory: ICategory) => {
    const category = await Category.findOneAndUpdate({ _id }, newCategory, { new: true });
    return category
}

export const deleteCategory = async (_id: String) => {
    await Category.deleteOne({ _id });
    return {}
}