import { Types } from "mongoose";

export interface IHall {
    _id: Types.ObjectId | string;
    name: string;
  }