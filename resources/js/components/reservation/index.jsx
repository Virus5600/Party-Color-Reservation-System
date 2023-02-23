import React, { useState } from 'react';
import { v4 as uuidv4 } from 'uuid';

import './style.css';

// React Router
import { redirect, Form, useLoaderData } from 'react-router-dom';

export async function action({ request }) {
	const formData = await request.formData();
	const reservationInfo = Object.fromEntries(formData);

	sessionStorage.setItem('reservationInfo', JSON.stringify(reservationInfo));
	
	return redirect('/reservation/confirm');
}

// LOADER
export function loader() {
	const session_data = sessionStorage.getItem('reservationInfo'); sessionStorage.removeItem('reservationInfo');
	
	if (session_data == null)
		return {};

	return JSON.parse(session_data);
}

// RESERVATION
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

// FORM
const ReservationDetails = () => {
	const reservationInfo = useLoaderData();

	return (
		<Form method='post' className='px-sm-5 p-4'>
			{/* 
			FormData
			 - first_name
			 - last_name
			 - email
			 - phone
			 - adult_senior
			 - junior
			 - elementary
			 - date
			 - starting_time
			 - extension
			 - special_request
			*/}

			{/*
			Validation Keys:
			 - booking_date => date
			 - pax => [adult_senior, junior, elementary]
			 - price => [???] -> No Price field? How would the customers know how much they should b=pay for the reservation?
			 - time_hour => starting_time:H
			 - time_min => starting_time:i
			 - booking_time => starting_time
			 - extension => extension
			 - menu => [???] -> Will probably need to do conditional shits for this at the backend...
			 - amount => [adult_senior, junior, elementary]
			 - phone_numbers => phone -> Convert to array
			 - contact_name => first_name + last_name
			 - contact_email => email -> convert to array
			 - special_request => special_request
			*/}

			<InputRow inputs={[
				// First Name
				{ type: 'text', name: 'first_name', label: 'First Name', isRequired: true, reservationInfo, },
				// Last Name
				{ type: 'text', name: 'last_name', label: 'Last Name', isRequired: true, reservationInfo, },
			]} />

			<InputRow inputs={[
				// Email
				{ type: 'email', name: 'email', label: 'Email', placeholder: 'myname@example.com', isRequired: true, reservationInfo, },
			]} />

			<InputRow inputs={[
				// Contact Number
				{ type: 'text', name: 'phone', label: 'Phone', placeholder: '090-1234-5678', isRequired: true, reservationInfo, },
			]} />

			<InputRow inputs={[
				// Adult / Senior
				{ type: 'number', name: 'adult_senior', label: 'Adult/Senior', placeholder: 'pax', min_value: 0, isRequired: false, reservationInfo, },
				// Junior
				{ type: 'number', name: 'junior', label: 'Junior', placeholder: 'pax', min_value: 0, isRequired: false, reservationInfo, },
				// Elementary
				{ type: 'number', name: 'elementary', label: 'Elementary', placeholder: 'pax', min_value: 0, isRequired: false, reservationInfo, },
			]} />

			<InputRow inputs={[
				// Date
				{ type: 'date', name: 'date', label: 'Date', isRequired: true, reservationInfo, },
				// Time
				{ type: 'time', name: 'starting_time', label: 'Start Time', min_value: '17:00', max_value: '19:00', isRequired: true, reservationInfo, },
			]} />

			<InputRow inputs={[
				// Extension
				{ type: 'number', name: 'extension', label: 'Time Extension', min_value: 0, isRequired: false, reservationInfo, },
			]} />

			<InputRow inputs={[
				// Special Request
				{ type: 'textArea', name: 'special_request', label: 'Special Requests', placeholder: 'If any', isRequired: false, reservationInfo },
			]} />

			<div className='text-end mt-4'>
				<button className='btn btn-success review_button' type='submit'>Review</button>
			</div>
		</Form>
	);
}

// INPUTS
const InputRow = ({ inputs }) => {
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
						onCountChange={input.onCountChange}
						isRequired={input.isRequired}
						reservationInfo={input.reservationInfo}
					/>
				)
			}
		</div>
	);
};

// FIELDS
const InputField = ({ type, name, label, placeholder, min, max, isRequired, reservationInfo }) => {
	let field;
	if (type == "textarea")
		field = <textarea name={name} defaultValue={reservationInfo[name] == null ? '' : reservationInfo[name]} placeholder={placeholder} required={isRequired} className="form-control" id={name}></textarea>
	else
		field = <input type={type} name={name} defaultValue={reservationInfo[name] == null ? '' : reservationInfo[name]} placeholder={placeholder} min={min} max={max} required={isRequired} className="form-control" id={name}></input>

	return (
		<div className="col-md">
			<label htmlFor={name} className="form-label text-white">{label}</label>
			
			{isRequired ? '' : <span className='text-white text-opacity-50 fw-light'> (Optional)</span>}
			
			{field}
		</div>
	);
};