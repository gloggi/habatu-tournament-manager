import { Request, Response } from 'express';
import { createTimeslot as create, getTimeslots as gets, updateTimeslots as update, getTimeslot as get, deleteTimeslot as remove } from "../services/timeslot.service";

export const createTimeslot = async (req: Request, res: Response) => {
    const timeslot = await create(req.body)
    res.json(timeslot)
}
export const getTimeslot = async (req: Request, res: Response) => {
    const timeslot = await get(req.params.id)
    res.json(timeslot)
}
export const getTimeslots = async (req: Request, res: Response) => {
    const timeslots = await gets()
    res.json(timeslots)
}
export const updateTimeslot = async (req: Request, res: Response) => {
    const timeslot = await update(req.params.id, req.body)
    res.json(timeslot)
}
export const deleteTimeslot = async (req: Request, res: Response) => {
    const timeslot = await remove(req.params.id)
    res.json(timeslot)
}
