import { Timeslot } from "../models";
import { ITimeslot } from "../interfaces/timeslot.interface";
import { Types } from "mongoose";

export const createTimeslot = async (timeslot: ITimeslot) => {
  return await Timeslot.create(timeslot);
};
export const getTimeslot = async (_id: string) => {
  if (!Types.ObjectId.isValid(_id)) {
    throw new Error("");
  }
  const timeslot = await Timeslot.findOne({ _id });
  return timeslot;
};
export const getTimeslots = async () => {
  const timeslots = await Timeslot.find({});
  return timeslots;
};

export const updateTimeslots = async (_id: String, newTimeslot: ITimeslot) => {
  const timeslot = await Timeslot.findOneAndUpdate({ _id }, newTimeslot, {
    new: true,
  });
  return timeslot;
};

export const deleteTimeslot = async (_id: String) => {
  await Timeslot.deleteOne({ _id });
  return {};
};
