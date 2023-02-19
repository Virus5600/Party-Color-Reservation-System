import './style.css';

import { useState } from 'react';

import axios from 'axios';

// components
import ReservationConfirmation from '../reservationconfirmation/index';

import { redirect } from 'react-router-dom';


// under development
export async function action() {
    const API = '';
    await axios.post(`${API}`).then(res => console.log(res)).catch(res => console.log(res));
    return redirect('');
}


export default function ReservationView() {

    const [reservationInfo, setReservationInfo] = useState();


    const handleViewClick = (control_number) => {
        // setReservationInfo(getReservationInfo(control_number));




        // for testing
        const reservationInfo = {
            first_name: 'Kenji',
            last_name: 'Sugino',
            email: 'krimssmirk003@gmail.com',
            phone: '090-4234-4324',
            no_guests: '2',
            date: '2022-02-20',
            starting_time: '12:23',
            time_extension: '3',
            special_request: '',
        };
        setReservationInfo(reservationInfo)



    }

    async function getReservationInfo(control_number) {
        const API = '';
        const reservationInfo = await axios.get(`${API}`).then(res => console.log(res)).catch(res => console.log(res));
        return reservationInfo;
    }


    return (
        <>
            <div className='ReservationView'>
                <ViewReservation onViewClick={handleViewClick} />
                <hr />
                {
                    reservationInfo == null ? <></> : <ReservationConfirmation forViewReservation={true} reservationInfo={reservationInfo} />
                }
            </div>
        </>
    );
}


const ViewReservation = ({ onViewClick }) => {

    const [controlNumber, setControlNumber] = useState('');

    const handleInputControlNumber = (event) => {
        setControlNumber(event.target.value);
    }

    const handleViewButton = () => {
        onViewClick(controlNumber);
    }


    return (
        <div className='ViewReservation'>
            <div className='ViewReservation-input'>
                <input type='text' onChange={handleInputControlNumber} /><br />
                <span >enter the control number to view the reservation</span>
            </div>
            <div className='ViewReservation-buttons'>
                <button onClick={handleViewButton}>view</button>
            </div>


        </div>
    );
}