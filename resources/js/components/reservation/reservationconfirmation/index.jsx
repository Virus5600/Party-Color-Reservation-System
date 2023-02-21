import '../style.css';

import axios from 'axios';

import { useLoaderData, Form, Link, redirect, } from 'react-router-dom';

export function loader() {
    const rawdata = sessionStorage.getItem('reservationInfo');
    return JSON.parse(rawdata);
}

export async function action() {
    const raw_session_data = sessionStorage.getItem('reservationInfo');
    const reservationInfo = JSON.parse(raw_session_data);
    const isSuccess = await handleReserveClick(reservationInfo);
    if (isSuccess == true) {
        const reservationsuccess = true;
        sessionStorage.setItem('reservationsuccess', JSON.stringify(reservationsuccess));
        return redirect('/reservation/success');
    }


    return redirect('/reservation');
}

export default function ReservationConfirmation(other) {
    var reservationInfo;

    if (other.forViewReservation == true) { // to use in reservationview component
        reservationInfo = other.reservationInfo;
    } else {
        reservationInfo = useLoaderData();
    }


    const {
        first_name,
        last_name,
        email,
        phone,
        adult_senior,
        junior,
        elementary,
        date,
        starting_time,
        time_extension,
        special_request,
    } = reservationInfo;


    return (

        <div className='container container-small'>
            <div className='background m-5'>
                <h1 className='text-center text-white'>Reservation Details</h1>

                <Form method='post' className='px-sm-5 p-4'>
                    <div className="text-white">
                        <FieldValue label={'Full Name'} data={first_name + ' ' + last_name} />
                        <FieldValue label={'Email'} data={email} />
                        <FieldValue label={'Phone'} data={phone} />
                        <FieldValue label={'adult/senior'} data={adult_senior} />
                        <FieldValue label={'junior'} data={junior} />
                        <FieldValue label={'elementary'} data={elementary} />
                        <FieldValue label={'Reservation'} data={date + ' ' + starting_time} />
                        <FieldValue label={'Time Extension'} data={time_extension} />
                        <FieldValue label={'Special Requests'} data={special_request} />
                    </div>
                    {
                        other.forViewReservation ? <ReservationButtonsForView /> : <ReservationButtons />
                    }
                </Form>
            </div>

        </div>
    );
}



const ReservationButtons = () => {
    return (
        <div className='text-end mt-4'>
            <Link to='/reservation'><button className='btn btn-danger me-2' >edit</button></Link>
            <button className='btn btn-success' type='submit'>reserve</button>
        </div>

    );
}

const ReservationButtonsForView = () => {
    return (
        <div className='text-end mt-4'>
            <button className='btn btn-danger' type='submit'>cancel request</button>
        </div>
    );
}


const FieldValue = ({ label, data }) => {

    return (
        <div className='row mb-3'>
            <div className='col-5 text-end' style={{ fontWeight: '900' }}>
                {label}:
            </div>
            <div className='col'>
                {data == '' ? 'N/A' : data}
            </div>
        </div>
    );

}


const handleReserveClick = async ({
    first_name,
    last_name,
    email,
    phone,
    adult_senior,
    junior,
    elementary,
    date,
    starting_time,
    time_extension,
    special_request,
}) => {

    let isSuccess = false;

    const token = document.querySelector('meta[name=csrf-token]').content;

    const API_TO_SEND_RESERVATION = '/api/react/bookings/create';


    const result = await axios.post(API_TO_SEND_RESERVATION, {
        _token: token,
        contact_name: [first_name + ' ' + last_name],
        contact_email: [email],
        phone_numbers: phone,
        pax: Number(adult_senior) + Number(junior) + Number(elementary),
        booking_date: date,
        time_hour: Number(starting_time.split(':')[0]),
        time_min: Number(starting_time.split(':')[1]),
        booking_time: Number(starting_time.split(':')[0]),
        extension: time_extension == '' ? 0 : Number(time_extension),
        specialRequest: special_request,


        // static as of now
        price: 3000,
        menu: [1],
        subscribed: '',
    }).then(response => {
        // do something after send the data to backend
        console.log(response);
        const data = response.data
        if (data.success) {
            sessionStorage.removeItem('reservationInfo');
            isSuccess = true;
            alert('success');
        } else {
            alert('internal error');
            for (const key in data.errors) {
                if (data.errors.hasOwnProperty(key)) {
                    alert(`${data.errors[key]}`);
                }
            }
        }


    }).catch(response => {
        console.log(response)
        alert('internal error');

    });
    return isSuccess;
}