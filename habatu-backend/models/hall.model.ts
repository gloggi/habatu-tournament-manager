import { Schema, model, connect } from "mongoose";
import { IHall } from "../interfaces/hall.interface";

const schema = new Schema<IHall>({
  name: { type: String, required: true },
});
export const Hall = model<IHall>("Hall", schema);
