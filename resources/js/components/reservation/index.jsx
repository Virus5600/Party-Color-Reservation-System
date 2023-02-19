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
		<div className='container container-small'>
			<div className='background m-5'>
				<h1 className='text-center text-white'>Reservation Details</h1>
				<ReservationDetails />
			</div>
		</div>
	);
}


const ReservationDetails = () => {
	const reservationInfo = useLoaderData();
	return (
		<Form method='post' className='px-sm-5 p-4'>

      <InputRow isRequired={true} inputs={[
        { type: 'text', name: 'first_name', label: 'First Name', reservationInfo, },
        { type: 'text', name: 'last_name', label: 'Last Name', reservationInfo, },
      ]} />

      <InputRow isRequired={true} inputs={[
        { type: 'email', name: 'email', label: 'Email', placeholder: 'ex: myname@example.com', colspan: '2', reservationInfo, },
      ]} />

      <InputRow isRequired={true} inputs={[
        { type: 'text', name: 'phone', label: 'Phone', colspan: '2', reservationInfo, },
      ]} />

      <InputRow isRequired={true} inputs={[
        { type: 'number', name: 'no_guests', label: 'Guest Count', colspan: '2', min_value: 1, reservationInfo, },
        { type: 'date', name: 'date', label: 'Date', reservationInfo, },
        { type: 'time', name: 'starting_time', label: 'Start Time', min_value: '17:00', max_value: '19:00', reservationInfo, },
      ]} />

      <InputRow isRequired={false} inputs={[
        { type: 'number', name: 'time_extension', label: 'Time Extension', colspan: '2', min_value: 1, reservationInfo, },
      ]} />

      <InputRow isRequired={false} inputs={[
        { type: 'text', name: 'special_request', label: 'Special Requests', colspan: '2', reservationInfo, },
      ]} />
      
      <div className='text-end mt-4'>
			  <button className='btn btn-success review_button' type='submit'>Review</button>
      </div>
		</Form>
	);
}


const InputRow = ({ inputs, isRequired }) => {
	return (
      <div className='row g-4 mb-3'>
        {
          inputs.map(input =>
            <InputField
              key={uuidv4()}
              type={input.type}
              name={input.name}
              label={input.label}
              placeholder={input.placeholder}
              min={input.min_value}
              max={input.max_value}
              colspan={input.colspan}
              onCountChange={input.onCountChange}
              isRequired={isRequired}
              reservationInfo={input.reservationInfo}
            />)
          }
      </div>
	);
};

const InputField = ({ type, name, label, placeholder, min, max, isRequired, reservationInfo }) => {
	return (
		<div className="col-md">
      <label for={name} className="form-label text-white">{label}</label>
      {isRequired ? '' : <a className='text-white text-opacity-50 fw-light'> (Optional)</a>}
      <input type={type} name={name} defaultValue={reservationInfo[name] == null ? '' : reservationInfo[name]} placeholder={placeholder} min={min} max={max} required={isRequired} className="form-control" id={name}></input>
    </div>
	);
};