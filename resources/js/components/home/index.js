import React from 'react';

import mainImage from './img/mainImage.png';

import './style.css';

// components
import QuickReservation from '../reservation/quickreservation';
import Announcement from '../announcement';
import AboutUs from '../aboutus';


const Home = ({ onReservationClick, onAnnouncementClick }) => {
    return (
        <div className='Home' id='Home'>
            <div className=''>
                <img src={mainImage} className="img-fluid"/>
            </div>
            <QuickReservation onReservationClick={onReservationClick} />
            <Announcement onAnnouncementClick={onAnnouncementClick} />
            <AboutUs />
        </div>
    );
};




export default Home;