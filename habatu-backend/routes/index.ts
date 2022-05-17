import { router as sectionRoute } from "./section.router";
import { router as teamRoute } from "./team.router";
import { router as categoryRoute } from "./category.router";
import { router as gameRoute } from "./game.router";
import { router as hallRouter } from "./hall.router";
import { Router } from "express";

export const router: Router = Router();

const routes: {path: string, route:Router}[] = [
    {
        path: "/sections",
        route: sectionRoute

    },
    {
        path: "/teams",
        route: teamRoute

    },
    {
        path: "/categories",
        route: categoryRoute

    },
    {
        path: "/games",
        route: gameRoute

    },
    {
        path: "/halls",
        route: hallRouter

    }
]

for(let route of routes){
    router.use(route.path, route.route)
}