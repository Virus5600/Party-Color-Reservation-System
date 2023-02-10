import React from 'react';

// icons
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faClock, faPhone, faLocationArrow } from '@fortawesome/free-solid-svg-icons';

import locationImage from './img/location.png';
import appearanceImage from './img/appearance.png';

const AboutUs = () => {
    return (
        <div className='AboutUs' id='AboutUs'>
            <h1>ABOUT US</h1>
            <div className='d-flex justify-content-evenly'>
                <TimeLocation />
                <Appearance />
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
            <div className='AboutUs-description d-flex adjustment'>
                <div className='d-flex '>
                    <FontAwesomeIcon icon={faClock} className='icon' />
                    <p>17:00 - 22:00</p>
                </div>
                <div>
                    <p className='closing'>CLOSED MONDAY/TUESDAY</p>
                </div>
            </div>
            <hr />
            <div className='AboutUs-description d-flex m-2'>
                <FontAwesomeIcon icon={faPhone} className='icon' />
                <p>080-3980-4560</p>
            </div>
            <hr />
            <div className='AboutUs-description '>
                <div className='d-flex m-2'>
                    <FontAwesomeIcon icon={faLocationArrow} className='icon' />
                    <p>3F, 1 Chome-2-12 Tsuboya, Naha, Okinawa 902-0065, Japan</p>
                </div>
                <div>
                    <img className='img-fluid' src={locationImage} alt='location map of party color' />
                </div>
            </div>
        </div>
    );
};

export default AboutUs;