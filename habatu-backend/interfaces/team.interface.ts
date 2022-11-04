import { Types } from "mongoose";
export interface ITeam {
  _id: Types.ObjectId | string;
  name: string;
  section?: string;
  category?: string;
}
