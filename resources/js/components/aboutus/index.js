import React from 'react';

// icons
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faClock, faPhone, faLocationArrow } from '@fortawesome/free-solid-svg-icons';

import locationImage from './img/location.png';
import appearanceImage from './img/appearance.png';

import './style.css';

const AboutUs = () => {
    return (
        <div className='AboutUs' id='AboutUs'>
            <div class='container'>
                <h1>About Us</h1>
                <div className='row'>
                    <div class='col'>
                        <TimeLocation />
                    </div>
                    <div class='col-md mt-3'>
                        <Appearance />
                    </div>
                </div>
            </div>

        </div>
    );
};

const Appearance = () => {
    return (
        <div className='Appearance'>
            <h2>APPEARANCE</h2>
            <div className='Appearance-image'>
                <img className='img-fluid' src={appearanceImage} alt='appearance' />
            </div>

        </div>
    );
};

const TimeLocation = () => {
    return (
        <div className='time-location'>
            <div class='row'>
                <div class='col-auto'>
                    <FontAwesomeIcon icon={faClock} className='icon' />
                </div>
                <div class='col'>
                    17:00 - 22:00<br />
                    CLOSED MONDAY/TUESDAY
                </div>
            </div>
            <hr />
            <div class='row'>
                <div class='col-auto'>
                    <FontAwesomeIcon icon={faPhone} className='icon' />
                </div>
                <div class='col'>
                    080-3980-4560<br />
                </div>
            </div>
            <hr />
            <div class='row'>
                <div class='col-auto'>
                    <FontAwesomeIcon icon={faLocationArrow} className='icon' />
                </div>
                <div class='col'>
                    3F, 1 Chome-2-12 Tsuboya, Naha, Okinawa 902-0065, Japan
                </div>
            </div>
            <hr />
            <div>
                <img className='img-fluid' src={locationImage} alt='location map of party color' />
            </div>
        </div>
    );
};

export default AboutUs;