import React from 'react';

import axios from 'axios';

// icons
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faClock, faPhone, faLocationArrow } from '@fortawesome/free-solid-svg-icons';

import locationImage from './img/location.png';
import appearanceImage from './img/appearance.png';

import './style.css';

async function fetchSettings() {

    const API_ENDPOINT = 'api/react/settings/fetch';

    const response = await axios.get(API_ENDPOINT);
    // console.log('response:', response);


    function isInDay(currentIdx, indices) {
        const idxArray = indices.split(',');
        for (var i = 0; i < idxArray.length; i++) {
            if (idxArray[i] == currentIdx) {
                return true;
            }
        }
        return false;
    }


    return {
        opening_time: response.data.settings[6].value,
        closing_time: response.data.settings[7].value,
        opening_day: response.data.days.filter((day, currentIdx) => { return isInDay(currentIdx, response.data.settings[8].value) }),
        contact_number: response.data.settings[4].value,
        address: response.data.settings[3].value,
        web_name: response.data.settings[1].value,
        web_description: response.data.settings[2].value,
        email: response.data.settings[5].value,
    };
}

const AboutUs = () => {

    const [settings, setSettings] = React.useState({});


    React.useEffect(() => {
        async function getSettings() {
            const data = await fetchSettings();
            // console.log('data:', data);
            setSettings(data);
        }
        getSettings();
    }, []);


    return (
        <div className='AboutUs' id='AboutUs'>
            <div className='container'>
                <h1>About Us</h1>
                <p className='text-center fs-1'>{settings.web_name}</p>
                <p className='text-center fs-5'>"{settings.web_description}"</p>
                <hr />
                <div className='row'>
                    <div className='col'>
                        <TimeLocation
                            opening_time={settings.opening_time}
                            closing_time={settings.closing_time}
                            opening_day={settings.opening_day}
                            contact_number={settings.contact_number}
                            address={settings.address}
                            email={settings.email}
                        />
                    </div>
                    <div className='col-md mt-3'>
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

const TimeLocation = React.memo((props) => {
    function arrangeOpeningDay(arr = []) {
        let opening_day_str = '';
        for (var i = 0; i < arr.length; i++) {
            if (i == arr.length - 1) {
                opening_day_str += arr[i];
            } else {
                opening_day_str += arr[i] + '/';
            }

        }
        return opening_day_str;
    }
    return (
        <div className='time-location'>
            <div className='row align-items-center my-2'>
                <div className='col-auto'>
                    <FontAwesomeIcon icon={faClock} className='h5 my-0' />
                </div>
                <div className='col'>
                    {props.opening_time} - {props.closing_time}<br />
                    <i>{arrangeOpeningDay(props.opening_day)}</i>
                </div>
            </div>
            <hr />
            <div className='row align-items-center my-2'>
                <div className='col-auto'>
                    <FontAwesomeIcon icon={faPhone} className='h5 my-0' />
                </div>
                <div className='col'>
                    {props.contact_number}
                </div>
            </div>
            <hr />
            <div className='row align-items-center my-2'>
                <div className='col-auto'>
                    <FontAwesomeIcon icon='fa-solid fa-envelope' className='h5 my-0' />
                </div>
                <div className='col'>
                    {props.email}
                </div>
            </div>
            <hr />
            <div className='row align-items-center my-2'>
                <div className='col-auto'>
                    <FontAwesomeIcon icon={faLocationArrow} className='h5 my-0' />
                </div>
                <div className='col'>
                    {props.address}
                </div>
            </div>
            <hr className='my-2' />
            <div>
                <img className='img-fluid' src={locationImage} alt='location map of party color' />
            </div>
        </div>
    );
});

export default AboutUs;