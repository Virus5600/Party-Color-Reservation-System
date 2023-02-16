import React, { useState } from 'react';

import { v4 as uuidv4 } from 'uuid';

import './style.css';

// react router
import { redirect, Form, useLoaderData } from 'react-router-dom';


export async function action({ request }) {
    const formData = await request.formData();
    const reservationInfo = Object.fromEntries(formData);
    sessionStorage.setItem('reservationInfo', JSON.stringify(reservationInfo));
    return redirect('/reservation/confirm');
}

export function loader() {
    const session_data = sessionStorage.getItem('reservationInfo')
    if (session_data == null) {
        return {};
    }
    return JSON.parse(session_data);
}


export default function Reservation() {

    return (
        <div className='Reservation'>
            <div className='TempReservation' >
                <h1 style={{ textAlign: 'center' }}>Reservation Details</h1>
                <ReservationDetails />
            </div>
        </div>
    );
}


const ReservationDetails = () => {
    const reservationInfo = useLoaderData();
    return (
        <div className='ReservationDetails'>
            <Form method='post'>
                <table className=''>

                    <tbody>

                        <InputContainerTR label={'Full Name'} isRequired={true} inputs={[
                            { type: 'text', name: 'first_name', label: 'First Name', inner_label: 'First Name', reservationInfo, },
                            { type: 'text', name: 'last_name', label: 'Last Name', inner_label: 'Last Name', reservationInfo, },
                        ]} />

                        <InputContainerTR label={'Email'} isRequired={true} inputs={[
                            { type: 'email', name: 'email', label: 'Email', placeholder: 'ex: myname@example.com', colspan: '2', reservationInfo, },
                        ]} />

                        <InputContainerTR label={'Phone'} isRequired={true} inputs={[
                            { type: 'text', name: 'phone', label: 'Phone', colspan: '2', reservationInfo, },
                        ]} />

                        <InputContainerTR label={'#ofGuests'} isRequired={true} inputs={[
                            { type: 'number', name: 'no_guests', label: '#ofGuests', colspan: '2', min_value: 1, reservationInfo, },
                        ]} />

                        <InputContainerTR label={'Reservation'} isRequired={true} inputs={[
                            { type: 'date', name: 'date', label: 'date', inner_label: 'Date', reservationInfo, },
                            { type: 'time', name: 'starting_time', label: 'starting_time', inner_label: 'Starting Time', min_value: '17:00', max_value: '19:00', reservationInfo, },
                        ]} />

                        <InputContainerTR label={'Time Extension'} isRequired={false} inputs={[
                            { type: 'number', name: 'time_extension', label: 'Time Extension', colspan: '2', min_value: 1, reservationInfo, },
                        ]} />

                        <InputContainerTR label={'Any Special Requests'} isRequired={false} inputs={[
                            { type: 'text', name: 'special_request', label: 'Any Special Requests', colspan: '2', reservationInfo, },
                        ]} />



                    </tbody>
                </table>
                <button className='btn btn-danger review_button' type='submit' >confirm</button>
            </Form>
        </div >
    );
}


const InputContainerTR = ({ label, inputs, isRequired }) => {
    return (
        <tr className='InputContainer'>

            <td >
                <span style={{ color: 'red' }}>{isRequired ? '*' : null}</span>
                <label> {label}</label>
            </td >

            {
                inputs.map(input =>
                    <Input
                        key={uuidv4()}
                        type={input.type}
                        name={input.name}
                        label={input.label}
                        inner_label={input.inner_label}
                        placeholder={input.placeholder}
                        min={input.min_value}
                        max={input.max_value}
                        colspan={input.colspan}
                        onCountChange={input.onCountChange}
                        isRequired={isRequired}
                        reservationInfo={input.reservationInfo}
                    />)
            }

        </tr >

    );
};


const Input = ({ type, name, label, inner_label, placeholder, min, max, colspan, isRequired, reservationInfo }) => {
    return (
        <td colSpan={colspan} className='Input'>
            <input type={type} name={name} defaultValue={reservationInfo[name] == null ? '' : reservationInfo[name]} placeholder={placeholder} min={min} max={max} required={isRequired} /><br />
            <span>{inner_label}</span>
        </td>
    );
};
