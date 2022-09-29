import { Types } from "mongoose";

export interface ITimeslot {
    _id?: Types.ObjectId | string;
    startTime: Date;
    endTime: Date;
  }