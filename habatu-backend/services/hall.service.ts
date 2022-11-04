import { Hall } from "../models";
import { IHall } from "../interfaces/hall.interface";
import { Types } from "mongoose";

export const createHall = async (hall: IHall) => {
  return await Hall.create(hall);
};
export const getHall = async (_id: string) => {
  if (!Types.ObjectId.isValid(_id)) {
    throw new Error("");
  }
  const hall = await Hall.findOne({ _id });
  return hall;
};
export const getHalls = async () => {
  const halls = await Hall.find({});
  return halls;
};

export const updateHalls = async (_id: String, newHall: IHall) => {
  const hall = await Hall.findOneAndUpdate({ _id }, newHall, { new: true });
  return hall;
};

export const deleteHall = async (_id: String) => {
  await Hall.deleteOne({ _id });
  return {};
};
