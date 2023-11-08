import { Request, Response } from "express";
import {
  createTeam as create,
  bulkCreateTeam as bulkCreate,
  getTeams as gets,
  updateTeams as update,
  getTeam as get,
  deleteTeam as remove,
} from "../services/team.service";
import { Types } from "mongoose";

export const createTeam = async (req: Request, res: Response) => {
  if (Array.isArray(req.body)) {
    const teams = await bulkCreate(req.body);
    res.json(teams);
  } else {
    const team = await create(req.body);
    res.json(team);
  }
};

export const getTeam = async (req: Request, res: Response) => {
  const team = await get(req.params.id);
  res.json(team);
};
export const getTeams = async (req: Request, res: Response) => {
  const teams = await gets();
  res.json(teams);
};
export const updateTeam = async (req: Request, res: Response) => {
  const team = await update(req.params.id, req.body);
  res.json(team);
};
export const deleteTeam = async (req: Request, res: Response) => {
  const team = await remove(req.params.id);
  res.json(team);
};
