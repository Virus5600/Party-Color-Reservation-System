import { first } from 'lodash';
import React, { useState, useEffect, useRef } from 'react';

import { v4 as uuidv4 } from 'uuid';

import './style.css';

import axios from 'axios';

// react router
import { redirect } from 'react-router-dom';


const TempReservation = () => {
    const [isReviewReserveClick, setReviewReserveClick] = useState(false);

    const handleReserveConfirmClick = () => {
        const firstName = JSON.parse(sessionStorage.getItem('First Name'));
        const lastName = JSON.parse(sessionStorage.getItem('Last Name'));
        const email = JSON.parse(sessionStorage.getItem('Email'));
        const phone = JSON.parse(sessionStorage.getItem('Phone'));
        const noGuests = JSON.parse(sessionStorage.getItem('#ofGuests'));
        const date = JSON.parse(sessionStorage.getItem('date'));
        const time = JSON.parse(sessionStorage.getItem('starting_time'));

        //  need flow if correct go else not!!
        if (firstName && lastName && email && phone && noGuests && date && time) {
            setReviewReserveClick(true);
        } else {
            alert('Please fill the required info!!');
        }

    };

    const handleEditClick = () => {
        setReviewReserveClick(false);
    };

    const handleReserveClick = async () => {


        // do something when reserve click -> send to backend the info
        // after serving the data to backend show some loading page
        const token = document.querySelector('meta[name=csrf-token]').content;

        const API_TO_SEND_RESERVATION = 'api/react/reservations/create';

        let flag = 0;
        const result = await axios.post(API_TO_SEND_RESERVATION, {
            _token: token,
            reservation_date: JSON.parse(sessionStorage.getItem('date'))['value'],
            pax: Number(JSON.parse(sessionStorage.getItem('#ofGuests'))['value']),
            price: 3000,  // static as of now
            time_hour: Number(JSON.parse(sessionStorage.getItem('starting_time'))['value'].split(':')[0]),
            time_min: Number(JSON.parse(sessionStorage.getItem('starting_time'))['value'].split(':')[1]),
            reservation_time: Number(JSON.parse(sessionStorage.getItem('starting_time'))['value'].split(':')[0]),
            extension: JSON.parse(sessionStorage.getItem('Time Extension')) == null ? 0 : Number(JSON.parse(sessionStorage.getItem('Time Extension'))['value']),
            menu: [1], // static as of now since plan has only one
            contact_name: [JSON.parse(sessionStorage.getItem('First Name'))['value'] + ' ' + JSON.parse(sessionStorage.getItem('Last Name'))['value']],
            contact_email: [JSON.parse(sessionStorage.getItem('Email'))['value']],
            phone_numbers: JSON.parse(sessionStorage.getItem('Phone'))['value'],
            specialRequest: JSON.parse(sessionStorage.getItem('Any Special Requests')) == null ? '' : JSON.parse(sessionStorage.getItem('Time Extension'))['value'],
            subscribed: '',
        }).then(response => {
            // do something after send the data to backend
            console.log(response);
            const data = response.data
            if (data.success) {
                sessionStorage.clear();
                alert('success');
                flag = 1;
            } else {
                var index = 0;
                for (const key in data.errors) {
                    if (data.errors.hasOwnProperty(key)) {
                        // console.log(`Index: ${index}, ${key}: ${data.errors[key]}`);
                        // index++;
                        alert(`${data.errors[key]}`);
                    }
                }
                setReviewReserveClick(false);
            }


        }).catch(response => {
            console.log(response)
            alert('internal error');

        });
        console.log('result:', result);

        if (flag) {
            redirect('/home');
        }

    };


    const opacity = isReviewReserveClick ? 0.2 : 0.5;

    return (
        <div className='Reservation'>
            <div className='TempReservation' style={{ backgroundColor: 'rgba(0, 0, 0, ' + opacity + ')' }}>
                <h1 style={{ textAlign: 'center' }}>Reservation Details</h1>

                {
                    isReviewReserveClick ? <ReservationDetailsConfirmation onEditClick={handleEditClick} onReserveClick={handleReserveClick} /> : <ReservationDetails onReserveConfirmClick={handleReserveConfirmClick} />
                }


            </div>
        </div>

    );
};

const ReservationDetailsConfirmation = ({ onEditClick, onReserveClick }) => {
    function getReservationDetails() {
        const firstName = JSON.parse(sessionStorage.getItem('First Name'))['value'];
        const lastName = JSON.parse(sessionStorage.getItem('Last Name'))['value'];
        const email = JSON.parse(sessionStorage.getItem('Email'))['value'];
        const phone = JSON.parse(sessionStorage.getItem('Phone'))['value'];
        const noGuests = JSON.parse(sessionStorage.getItem('#ofGuests'))['value'];
        const date = JSON.parse(sessionStorage.getItem('date'))['value'];
        const starting_time = JSON.parse(sessionStorage.getItem('starting_time'))['value'];
        const timeExtension = JSON.parse(sessionStorage.getItem('Time Extension')) == null ? 'NA' : JSON.parse(sessionStorage.getItem('Time Extension'))['value'];
        const specialRequests = JSON.parse(sessionStorage.getItem('Any Special Requests')) == null ? 'NA' : JSON.parse(sessionStorage.getItem('Any Special Requests'))['value'];

        return {
            firstName: firstName,
            lastName: lastName,
            email: email,
            phone: phone,
            noGuests: noGuests,
            date: date,
            starting_time: starting_time,
            timeExtension: timeExtension,
            specialRequests: specialRequests
        }
    };
    const reservation = getReservationDetails();
    return (
        <div className='ReservationDetailsConfirmation'>
            <table>
                <tbody>
                    <ReservationDetailsConfirmationTR label={'Full Name'} data={reservation.firstName + ' ' + reservation.lastName} />
                    <ReservationDetailsConfirmationTR label={'Email'} data={reservation.email} />
                    <ReservationDetailsConfirmationTR label={'Phone'} data={reservation.phone} />
                    <ReservationDetailsConfirmationTR label={'#ofGuests'} data={reservation.noGuests} />
                    <ReservationDetailsConfirmationTR label={'Reservation'} data={reservation.date + ' ' + reservation.starting_time} />
                    <ReservationDetailsConfirmationTR label={'Time Extension'} data={reservation.timeExtension} />
                    <ReservationDetailsConfirmationTR label={'Any Special Requests'} data={reservation.specialRequests} />

                </tbody>
            </table>
            <div className='ReservationDetailsConfirmation-buttons'>
                <button className='btn btn-danger' onClick={onEditClick}>edit</button>
                <button className='btn btn-success' onClick={onReserveClick}>reserve</button>
            </div>
        </div>
    );
};

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

};

const ReservationDetails = ({ onReserveConfirmClick }) => {


    return (
        <div className='ReservationDetails'>
            <table className=''>

                <tbody>

                    <InputContainerTR label={'Full Name'} isRequired={true} inputs={[
                        { type: 'text', label: 'First Name', inner_label: 'First Name', getKey: function () { return this.label; }, },
                        { type: 'text', label: 'Last Name', inner_label: 'Last Name', getKey: function () { return this.label; }, },
                    ]} />

                    <InputContainerTR label={'Email'} isRequired={true} inputs={[
                        { type: 'email', label: 'Email', placeholder: 'ex: myname@example.com', colspan: '2', getKey: function () { return this.label; }, },
                    ]} />

                    <InputContainerTR label={'Phone'} isRequired={true} inputs={[
                        { type: 'text', label: 'Phone', colspan: '2', getKey: function () { return this.label; }, },
                    ]} />

                    <InputContainerTR label={'#ofGuests'} isRequired={true} inputs={[
                        { type: 'number', label: '#ofGuests', colspan: '2', min_value: 1, getKey: function () { return this.label; }, },
                    ]} />

                    <InputContainerTR label={'Reservation'} isRequired={true} inputs={[
                        { type: 'date', label: 'date', inner_label: 'Date', getKey: function () { return this.label; }, },
                        { type: 'time', label: 'starting_time', inner_label: 'Starting Time', min_value: '17:00', max_value: '19:00', getKey: function () { return this.label; }, },
                    ]} />

                    <InputContainerTR label={'Time Extension'} isRequired={false} inputs={[
                        { type: 'number', label: 'Time Extension', colspan: '2', min_value: 1, getKey: function () { return this.label; }, },
                    ]} />

                    <InputContainerTR label={'Any Special Requests'} isRequired={false} inputs={[
                        { type: 'text', label: 'Any Special Requests', colspan: '2', getKey: function () { return this.label; }, },
                    ]} />



                </tbody>

            </table>
            <button className='btn btn-danger review_button' onClick={onReserveConfirmClick}>confirm</button>



        </div >
    );
};

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
                        inner_label={input.inner_label}
                        placeholder={input.placeholder}
                        min={input.min_value}
                        max={input.max_value}
                        colspan={input.colspan}
                        sessionKey={input.getKey()}
                        onCountChange={input.onCountChange}
                        isRequired={isRequired}
                    />)
            }

        </tr >

    );
};


const Input = ({ type, inner_label, placeholder, min, max, colspan, sessionKey, isRequired }) => {
    // type -> text, email, number, date, time

    const sessiondata = JSON.parse(sessionStorage.getItem(sessionKey));
    const actualdata = sessiondata == null ? '' : sessiondata['value']
    const [data, setData] = useState(type == 'date' || type == 'time' ? actualdata || new Date() : actualdata || '');
    const [isFill, setFill] = useState(true);


    const handleDataChange = event => {
        if (isRequired) {
            sessionStorage.setItem(sessionKey, '{"value":"' + event.target.value + '","isFill":true}');
        } else {
            sessionStorage.setItem(sessionKey, '{"value":"' + event.target.value + '"}');
        }


        setData(event.target.value);
    };



    const handleOnFocusOut = event => {
        if (event.target.value.length == 0) {
            if (isRequired) {
                // const obj = JSON.parse(sessionStorage.getItem(sessionKey));
                // obj['isFill'] = false;
                // sessionStorage.setItem(sessionKey, JSON.stringify(obj));
            }
            console.log('inside if');
            setFill(false);

        } else {
            setFill(true);

        }

    }

    const alertText = isFill ? '' : 'please fill';


    return (
        <td colSpan={colspan} className='Input'>

            <span style={{ color: 'red' }}>{alertText}</span>
            <input type={type} value={data} placeholder={placeholder} min={min} max={max} onChange={handleDataChange} onBlur={handleOnFocusOut} /><br />
            <span>{inner_label}</span>



        </td>
    );
};

export default TempReservation;