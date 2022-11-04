import { Request, Response } from "express";
import {
  createHall as create,
  getHalls as gets,
  updateHalls as update,
  getHall as get,
  deleteHall as remove,
} from "../services/hall.service";

export const createHall = async (req: Request, res: Response) => {
  const hall = await create(req.body);
  res.json(hall);
};
export const getHall = async (req: Request, res: Response) => {
  const hall = await get(req.params.id);
  res.json(hall);
};
export const getHalls = async (req: Request, res: Response) => {
  const halls = await gets();
  res.json(halls);
};
export const updateHall = async (req: Request, res: Response) => {
  const hall = await update(req.params.id, req.body);
  res.json(hall);
};
export const deleteHall = async (req: Request, res: Response) => {
  const hall = await remove(req.params.id);
  res.json(hall);
};
