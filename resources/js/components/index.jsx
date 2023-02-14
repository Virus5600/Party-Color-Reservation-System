import React from 'react';
import './index.css';
import ReactDOM from 'react-dom';

// for bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.min.js';

// components (pages)
import Home from './home';
import Nav from './navigation';
import TempReservation from './tempreservation';
import Announcement from './announcement';
import AnnouncementContent from './announcement/announcementcontent';
import AboutUs from './aboutus';

import {
    loaderLatest as loaderQuickAnnouncement,
    loader as loaderAnnouncement,
} from './announcement/index';

import {
    loader as loaderAnnouncementContent
} from './announcement/announcementcontent/index';

import {
    createBrowserRouter,
    RouterProvider,
} from 'react-router-dom';

const router = createBrowserRouter([
    {
        path: '/',
        element: <Nav />,
        children: [
            {
                path: 'home',
                element: <Home />,
                loader: loaderQuickAnnouncement,

            },
            {
                path: 'reservation',
                element: <TempReservation />,
            },
            {
                path: 'announcement',
                element: <Announcement />,
                loader: loaderAnnouncement,
            },
            {
                path: 'announcement/:announcementId',
                element: <AnnouncementContent />,
                loader: loaderAnnouncementContent,
            },
            {
                path: 'aboutus',
                element: <AboutUs />,
            },
        ],
    },
], {
    // basename: '/home/',
});



ReactDOM.render(
    <React.StrictMode>
        <RouterProvider router={router} />
    </React.StrictMode>,
    document.getElementById('app')
);

