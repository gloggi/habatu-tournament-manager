import express, { Router } from 'express';
import { createUser, deleteUser, getUser, getUsers, updateUser } from '../controllers/user.controller';
export const router: Router = express.Router();

router.route('/').post(createUser).get(getUsers)
router.route('/:id').put(updateUser).get(getUser).delete(deleteUser)