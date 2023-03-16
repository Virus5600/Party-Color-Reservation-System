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
	const session_data = sessionStorage.getItem('reservationInfo');

	if (session_data == null) {
		return {};
	}


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

	function getCurrentDate() {
		// return the current string date
		// e.g. '2023-02-27'
		const current_date = new Date();


		// setting the date tomorrow
		current_date.setDate(current_date.getDate() + 1);


		let year = current_date.getFullYear();
		let month = current_date.getMonth() + 1;
		let date = current_date.getDate();

		if (month < 10) month = '0' + month.toString();

		if (Number(date) < 10) date = '0' + date;

		return year + '-' + month + '-' + date;
	}

	// const customHooksForPax = () => {
	// 	const [total_pax, set_total_pax] = useState(0);


	// 	// this is for handling the pax inputs
	// 	return (no_as_string) => {
	// 		console.log('clicked pax:', no_as_string); // for testing / debugging
	// 		set_total_pax(prev => prev + Number(no_as_string));
	// 	};
	// }

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
				{ type: 'number', name: 'adult_senior', label: 'Adult/Senior', placeholder: 'pax', min_value: 1, isRequired: true, reservationInfo, },
				// Junior
				{ type: 'number', name: 'junior', label: 'Junior', placeholder: 'pax', min_value: 0, isRequired: false, reservationInfo, },
				// Elementary
				{ type: 'number', name: 'elementary', label: 'Elementary', placeholder: 'pax', min_value: 0, isRequired: false, reservationInfo, },
			]} />

			<InputRow inputs={[
				// Date
				{ type: 'date', name: 'date', label: 'Date', min_value: getCurrentDate(), isRequired: true, reservationInfo, },
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
const InputRow = (props) => {
	// console.log('others.customHooksForPax:', props.customHooksForPax);
	// let paxHandler;
	// if (props.customHooksForPax != undefined) {
	// 	paxHandler = props.customHooksForPax();
	// }

	return (
		<div className='row g-4 mb-3'>
			{
				props.inputs.map(input =>
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
					// onPaxClick={paxHandler}
					/>
				)
			}
		</div>
	);
};

// FIELDS
const InputField = (props) => {
	let field;
	if (props.type == "textarea")
		field = <textarea name={props.name} defaultValue={props.reservationInfo[props.name] == null ? '' : props.reservationInfo[props.name]} placeholder={props.placeholder} required={props.isRequired} className="form-control" id={props.name}></textarea>
	else
		field = <input
			type={props.type}
			name={props.name}
			defaultValue={props.reservationInfo[props.name] == null ? '' : props.reservationInfo[props.name]}
			placeholder={props.placeholder}
			min={props.min}
			max={props.max}
			required={props.isRequired}
			className="form-control"
			id={props.name}
		// onChange={props.onPaxClick == null ? null : (event) => {

		// 	props.onPaxClick(event.target.value)
		// }}
		/>

	return (
		<div className="col-md">
			<label htmlFor={props.name} className="form-label text-white">{props.label}</label>

			{props.isRequired ? '' : <span className='text-white text-opacity-50 fw-light'> (Optional)</span>}

			{field}
		</div>
	);
};