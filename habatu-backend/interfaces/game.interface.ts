import { Types } from "mongoose";

export interface IGame {
  _id?: Types.ObjectId | string;
  teamA: string;
  teamB: string;
  category: string;
  hall?: string;
  timeslot?: string;
  type: string;
  pointsTeamA: number;
  pointsTeamB: number;
}
