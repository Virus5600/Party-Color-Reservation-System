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
       - time_extension
       - special_request
      
      
      
      
      */}

      <InputRow inputs={[
        { type: 'text', name: 'first_name', label: 'First Name', isRequired: true, reservationInfo, },
        { type: 'text', name: 'last_name', label: 'Last Name', isRequired: true, reservationInfo, },
      ]} />

      <InputRow inputs={[
        { type: 'email', name: 'email', label: 'Email', placeholder: 'myname@example.com', isRequired: true, reservationInfo, },
      ]} />

      <InputRow inputs={[
        { type: 'text', name: 'phone', label: 'Phone', placeholder: '090-1234-5678', isRequired: true, reservationInfo, },
      ]} />

      <InputRow inputs={[
        { type: 'number', name: 'adult_senior', label: 'adult/senior', placeholder: 'pax', min_value: 0, isRequired: true, reservationInfo, },
        { type: 'number', name: 'junior', label: 'junior', placeholder: 'pax', min_value: 0, isRequired: true, reservationInfo, },
        { type: 'number', name: 'elementary', label: 'elementary', placeholder: 'pax', min_value: 0, isRequired: true, reservationInfo, },
      ]}
      />

      <InputRow inputs={[

        { type: 'date', name: 'date', label: 'Date', isRequired: true, reservationInfo, },
        { type: 'time', name: 'starting_time', label: 'Start Time', min_value: '17:00', max_value: '19:00', isRequired: true, reservationInfo, },
      ]} />

      <InputRow inputs={[
        { type: 'number', name: 'time_extension', label: 'Time Extension', min_value: 1, isRequired: false, reservationInfo, },
      ]} />

      <InputRow inputs={[
        { type: 'textArea', name: 'special_request', label: 'Special Requests', placeholder: 'If any', isRequired: false, reservationInfo },
      ]} />

      <div className='text-end mt-4'>
        <button className='btn btn-success review_button' type='submit'>Review</button>
      </div>
    </Form>
  );
}


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
          />)
      }
    </div>
  );
};

const InputField = ({ type, name, label, placeholder, min, max, isRequired, reservationInfo }) => {
  let field;
  if (type == "textarea") {
    field = <textarea name={name} defaultValue={reservationInfo[name] == null ? '' : reservationInfo[name]} placeholder={placeholder} required={isRequired} className="form-control" id={name}></textarea>
  } else {
    field = <input type={type} name={name} defaultValue={reservationInfo[name] == null ? '' : reservationInfo[name]} placeholder={placeholder} min={min} max={max} required={isRequired} className="form-control" id={name}></input>
  }
  return (
    <div className="col-md">
      <label htmlFor={name} className="form-label text-white">{label}</label>
      {isRequired ? '' : <a className='text-white text-opacity-50 fw-light'> (Optional)</a>}
      {field}
    </div>
  );
};