import { Request, Response } from 'express';
import { createSection as create, getSections as gets, updateSections as update, getSection as get, deleteSection as remove } from "../services/section.service";

export const createSection = async (req: Request, res: Response) => {
    const section = await create(req.body)
    res.json(section)
}
export const getSection = async (req: Request, res: Response) => {
    const section = await get(req.params.id)
    res.json(section)
}
export const getSections = async (req: Request, res: Response) => {
    const sections = await gets()
    res.json(sections)
}
export const updateSection = async (req: Request, res: Response) => {
    const section = await update(req.params.id, req.body)
    res.json(section)
}
export const deleteSection = async (req: Request, res: Response) => {
    const section = await remove(req.params.id)
    res.json(section)
}
