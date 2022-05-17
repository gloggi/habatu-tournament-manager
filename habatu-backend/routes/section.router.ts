import express, { Router } from 'express';
import { createSection, deleteSection, getSection, getSections, updateSection } from '../controllers/section.controller';
export const router: Router = express.Router();

router.route('/').post(createSection).get(getSections)
router.route('/:id').put(updateSection).get(getSection).delete(deleteSection)