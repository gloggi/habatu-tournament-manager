import express, { Router } from 'express';
import { createUser, deleteUser, getUser, getUsers, loginUser, updateUser } from '../controllers/user.controller';
import { adminMiddleware } from '../middlewares/middlewares';
export const router: Router = express.Router();

router.route('/').post(createUser).get(adminMiddleware, getUsers)
router.route('/login').post(loginUser)
router.route('/:id').put(updateUser).get(getUser).delete(deleteUser)