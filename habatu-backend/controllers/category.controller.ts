import { Request, Response } from "express";
import {
  createCategory as create,
  getCategorys as gets,
  updateCategorys as update,
  getCategory as get,
  deleteCategory as remove,
} from "../services/category.service";

export const createCategory = async (req: Request, res: Response) => {
  const category = await create(req.body);
  res.json(category);
};
export const getCategory = async (req: Request, res: Response) => {
  const category = await get(req.params.id);
  res.json(category);
};
export const getCategorys = async (req: Request, res: Response) => {
  const categorys = await gets();
  res.json(categorys);
};
export const updateCategory = async (req: Request, res: Response) => {
  const category = await update(req.params.id, req.body);
  res.json(category);
};
export const deleteCategory = async (req: Request, res: Response) => {
  const category = await remove(req.params.id);
  res.json(category);
};
