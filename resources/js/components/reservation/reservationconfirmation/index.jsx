import './style.css';

import axios from 'axios';

import { useLoaderData, Form, Link, redirect, } from 'react-router-dom';

export function loader() {
    const rawdata = sessionStorage.getItem('reservationInfo');
    return JSON.parse(rawdata);
}

export async function action() {
    const raw_session_data = sessionStorage.getItem('reservationInfo');
    const reservationInfo = JSON.parse(raw_session_data);
    await handleReserveClick(reservationInfo);
    return redirect('/reservation/success');

}

export default function ReservationConfirmation() {

    const reservationInfo = useLoaderData();

    const {
        first_name,
        last_name,
        email,
        phone,
        no_guests,
        date,
        starting_time,
        time_extension,
        special_request,
    } = reservationInfo;


    return (
        <div className='ReservationDetailsConfirmation'>
            <Form method='post'>
                <table>
                    <tbody>

                        <ReservationDetailsConfirmationTR label={'Full Name'} data={first_name + ' ' + last_name} />
                        <ReservationDetailsConfirmationTR label={'Email'} data={email} />
                        <ReservationDetailsConfirmationTR label={'Phone'} data={phone} />
                        <ReservationDetailsConfirmationTR label={'#ofGuests'} data={no_guests} />
                        <ReservationDetailsConfirmationTR label={'Reservation'} data={date + ' ' + starting_time} />
                        <ReservationDetailsConfirmationTR label={'Time Extension'} data={time_extension} />
                        <ReservationDetailsConfirmationTR label={'Any Special Requests'} data={special_request} />



                    </tbody>
                </table>
                <div className='ReservationDetailsConfirmation-buttons'>
                    <button className='btn btn-danger' ><Link to='/reservation'>edit</Link></button>
                    <button className='btn btn-success' type='submit'>reserve</button>
                </div>
            </Form>
        </div>
    );
}

const ReservationDetailsConfirmationTR = ({ label, data }) => {
    return (
        <tr className='ReservationDetailsConfirmationTR'>
            <td style={{ fontWeight: '900' }}>
                {label}
            </td>
            <td>
                {data == '' ? 'NA' : data}
            </td>
        </tr>
    );

}


const handleReserveClick = async ({
    first_name,
    last_name,
    email,
    phone,
    no_guests,
    date,
    starting_time,
    time_extension,
    special_request,
}) => {

    const token = document.querySelector('meta[name=csrf-token]').content;

    const API_TO_SEND_RESERVATION = '/api/react/reservations/create';


    const result = await axios.post(API_TO_SEND_RESERVATION, {
        _token: token,
        contact_name: [first_name + ' ' + last_name],
        contact_email: [email],
        phone_numbers: phone,
        pax: Number(no_guests),
        reservation_date: date,
        time_hour: Number(starting_time.split(':')[0]),
        time_min: Number(starting_time.split(':')[1]),
        reservation_time: Number(starting_time.split(':')[0]),
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
            alert('success');
        } else {
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
}
