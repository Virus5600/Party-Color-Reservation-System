import React from 'react';
import './index.css';
import ReactDOM from 'react-dom';

// for bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.min.js';

// components (pages)
import Home from './home';
import Nav from './navigation';
import ReservationSelection from './reservation/reservationselection';
import ReservationView from './reservation/reservationview';
import Reservation from './reservation';
import ReservationConfirmation from './reservation/reservationconfirmation';
import ReservationSuccess from './reservation/reservationsuccess';
import Announcement from './announcement';
import AnnouncementContent from './announcement/announcementcontent/index';
import AboutUs from './aboutus';

import {
    loaderLatest as loaderQuickAnnouncement,
    loader as loaderAnnouncement,
} from './announcement/index';

import {
    loader as loaderAnnouncementContent
} from './announcement/announcementcontent/index';

import {
    loader as reservationLoader
} from './reservation/index';

import {
    action as confirmAction,
} from './reservation/index';

import {
    loader as reservationInfoLoader,
} from './reservation/reservationconfirmation/index';

import {
    action as reserveAction,
} from './reservation/reservationconfirmation/index';

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
                path: '/',
                element: <Home />,
                loader: loaderQuickAnnouncement,

            },
            {
                path: 'reservationselection',
                element: <ReservationSelection />,
            },
            {
                path: 'reservation',
                element: <Reservation />,
                loader: reservationLoader,
                action: confirmAction,
            },
            {
                path: '/viewreservation',
                element: <ReservationView />,
            },
            {
                path: 'reservation/confirm',
                element: <ReservationConfirmation />,
                loader: reservationInfoLoader,
                action: reserveAction,
            },
            {
                path: 'reservation/success',
                element: <ReservationSuccess
                    title={'Your reservation has been confirmed!'}
                    description={'An email confirmation has been sent to you.'}
                    linkLabel={'make'}
                    link={'/reservation'}
                    backgroundStyle={{ backgroundColor: '#1D7B3E' }}
                    iconStyle={{ color: '#00ff59a1' }}
                    isSuccess={true}

                />,
            },
            {
                path: 'reservation/cancel',
                element: <ReservationSuccess
                    title={'Your cancel request has been sent!'}
                    description={'We will inform you about your cancel request sooner'}
                    linkLabel={'view'}
                    link={'/viewreservation'}
                    backgroundStyle={{ backgroundColor: '#B83939' }}
                    iconStyle={{ color: '#871A1A' }}
                    isSuccess={false}
                />,

            },
            {
                path: 'announcements',
                element: <Announcement />,
                loader: loaderAnnouncement,
            },
            {
                path: 'announcements/:announcementId',
                element: <AnnouncementContent />,
                loader: loaderAnnouncementContent,
            },
            {
                path: 'about-us',
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