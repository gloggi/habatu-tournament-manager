import { Request, Response } from 'express';
import { getGamesPreview as getsPreview, getTimePreview as timePreview, getTournamentTable as getTable, getTournamentRanking as getRanking, createFinals, getTableByGroupId, specifyReferee } from "../services/tournament.service";
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

export const getTournamentRanking = async (req: Request, res: Response) => {
    const ranking = await getRanking()
    res.json(ranking)
}
export const createTournamentFinals = async (req: Request, res: Response) => {
    await createFinals()
    res.json({message: "Finals Created"})
}
export const getGroupTable = async (req: Request, res: Response) => {
    const table = await getTableByGroupId(req.params.id)
    res.json(table)
}

export const addGameToReferee = async (req: Request, res: Response) => {
    const gameId = req.body.gameId
    const userId = req.body.userId
    await specifyReferee(gameId, userId);
    res.status(201).json({message: "Game has been added"})
}