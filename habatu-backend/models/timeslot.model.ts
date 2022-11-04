import { Schema, model, connect } from "mongoose";
import { ITimeslot } from "../interfaces/timeslot.interface";
import autopopulate from "mongoose-autopopulate";

const schema = new Schema<ITimeslot>({
  startTime: { type: Date },
  endTime: { type: Date },
});
schema.plugin(autopopulate);
export const Timeslot = model<ITimeslot>("Timeslot", schema);
