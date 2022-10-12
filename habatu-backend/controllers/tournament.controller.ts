import { Request, Response } from 'express';
import { getGamesPreview as getsPreview, getTimePreview as timePreview, getTournamentTable as getTable } from "../services/tournament.service";
export const getGamesPreview = async (req: Request, res: Response) => {
    const games = await getsPreview()
    res.json(games)
}

export const getTimePreview = async (req: Request, res: Response) => {
    const preview = await timePreview()
    res.json(preview)
}

export const getTournamentTable = async (req: Request, res: Response) => {
    const games = await getTable()
    res.json(games)
}