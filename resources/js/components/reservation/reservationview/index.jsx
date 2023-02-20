import './style.css';

import { useState } from 'react';

import axios from 'axios';

// components
import ReservationConfirmation from '../reservationconfirmation/index';

import { redirect } from 'react-router-dom';


// under development
export async function action() {
    const API = '';
    const result = await axios.post(`${API}`).then(res => console.log(res)).catch(res => console.log(res));
    return redirect('/reservation/cancel');
}


export default function ReservationView() {

    const [reservationInfo, setReservationInfo] = useState();


    const handleViewClick = (control_number) => {
        // sessionStorage.setItem('reservationInfo', JSON.stringify(reservationInfo));
        // setReservationInfo(getReservationInfo(control_number));




        // remove this after implementing API view reservation
        // for testing ///////////////////////////////
        const reservationInfo = {
            first_name: 'Kenji',
            last_name: 'Sugino',
            email: 'krimssmirk003@gmail.com',
            phone: '090-4234-4324',
            adult_senior: '2',
            junior: '2',
            elementary: '3',
            date: '2022-02-20',
            starting_time: '12:23',
            time_extension: '3',
            special_request: '',
        };
        setReservationInfo(reservationInfo)
        ///////////////////////////////////////////////


    }

    async function getReservationInfo(control_number) {
        const API = '';
        const reservationInfo = await axios.get(`${API}`).then(res => console.log(res)).catch(res => console.log(res));
        return reservationInfo;
    }


    return (
        <div>
            <ViewReservation onViewClick={handleViewClick} />
            <hr />
            {
                reservationInfo == null ? <></> : <ReservationConfirmation forViewReservation={true} reservationInfo={reservationInfo} />
            }
        </div>
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
        <div className='container container-small'>
            <div className="m-5">
                <label htmlFor='cnum' className="form-label">Control Number</label>
                <div className="row g-2">
                    <div className='col'>
                        <input type='text' placeholder='Input your control number here...' onChange={handleInputControlNumber} className='form-control my-0' id='cnum'/>
                    </div>
                    <div className='col-sm-auto text-end'>
                        <button onClick={handleViewButton} className='btn btn-success' >View Reservation</button>
                    </div>
                </div>
            </div>
        </div>
    );
}