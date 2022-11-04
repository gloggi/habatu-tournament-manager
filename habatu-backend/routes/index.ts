import { router as sectionRoute } from "./section.router";
import { router as teamRoute } from "./team.router";
import { router as categoryRoute } from "./category.router";
import { router as gameRoute } from "./game.router";
import { router as hallRouter } from "./hall.router";
import { router as optionRouter } from "./option.router";
import { router as timeslotRouter } from "./timeslot.router";
import { router as userRouter } from "./user.router";
import { router as tournamentRouter } from "./tournament.router";
import { Router } from "express";

export const router: Router = Router();

const routes: { path: string; route: Router }[] = [
  {
    path: "/sections",
    route: sectionRoute,
  },
  {
    path: "/teams",
    route: teamRoute,
  },
  {
    path: "/categories",
    route: categoryRoute,
  },
  {
    path: "/games",
    route: gameRoute,
  },
  {
    path: "/halls",
    route: hallRouter,
  },
  {
    path: "/options",
    route: optionRouter,
  },
  {
    path: "/timeslots",
    route: timeslotRouter,
  },
  {
    path: "/users",
    route: userRouter,
  },
  {
    path: "/tournament",
    route: tournamentRouter,
  },
];

for (let route of routes) {
  router.use(route.path, route.route);
}
