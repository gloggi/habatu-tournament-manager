import { Team } from "../models";
import { ITeam } from "../interfaces/team.interface";
import { Types } from "mongoose";

export const createTeam = async (team: ITeam) => {
  return await Team.create(team);
};
export const getTeam = async (_id: string) => {
  if (!Types.ObjectId.isValid(_id)) {
    throw new Error("");
  }
  const team = await Team.findById(_id);
  return team;
};
export const getTeams = async () => {
  const teams = await Team.find({});
  return teams;
};

export const updateTeams = async (_id: String, newTeam: ITeam) => {
  const team = await Team.findOneAndUpdate({ _id }, newTeam, { new: true });
  return team;
};

export const deleteTeam = async (_id: String) => {
  await Team.deleteOne({ _id });
  return {};
};
