// react
import React from 'react';
import ReactDOM from 'react-dom';


// for bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.min.js';

import './index.css';


// components (pages)
import Home from './home';
import Nav from './navigation';

import ReservationSelection from './reservation/reservationselection';
import ReservationView from './reservation/reservationview';
import Reservation from './reservation';
import ReservationConfirmation from './reservation/reservationconfirmation';

import Announcement from './announcement';
import AnnouncementContent from './announcement/announcementcontent/index';
import AboutUs from './aboutus';


// react-router-dom loader/action
import { loader as reservationLoader } from './reservation/index';
import { action as confirmAction } from './reservation/index';
import { loader as reservationInfoLoader } from './reservation/reservationconfirmation/index';

import { loader as loaderAnnouncementContent } from './announcement/announcementcontent/index';


// react-router-dom routing
import { createBrowserRouter, RouterProvider } from 'react-router-dom';


const router = createBrowserRouter([
    {
        path: '/',
        element: <Nav />,
        children: [
            {
                path: '/',
                element: <Home />,

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
                // action: reserveAction,
            },
            // {
            //     path: 'reservation/success',
            //     element: <ReservationSuccess
            //         title={'Your reservation has been confirmed!'}
            //         description={'An email confirmation has been sent to you.'}
            //         linkLabel={'make'}
            //         link={'/reservation'}
            //         backgroundStyle={{ backgroundColor: '#1D7B3E' }}
            //         iconStyle={{ color: '#00ff59a1' }}
            //         isSuccess={true}

            //     />,
            //     loader: successCancelLoader,
            // },
            // {
            //     path: 'reservation/cancel',
            //     element: <ReservationSuccess
            //         title={'Your cancel request has been sent!'}
            //         description={'We will inform you about your cancel request sooner'}
            //         linkLabel={'view'}
            //         link={'/viewreservation'}
            //         backgroundStyle={{ backgroundColor: '#B83939' }}
            //         iconStyle={{ color: '#871A1A' }}
            //         isSuccess={false}
            //     />,
            //     loader: successCancelLoader,

            // },
            {
                path: 'announcements',
                element: <Announcement />,
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
]);


// mounting to where the html in laravel
ReactDOM.render(
    <React.StrictMode>
        <RouterProvider router={router} />
    </React.StrictMode>,
    document.getElementById('app')
);