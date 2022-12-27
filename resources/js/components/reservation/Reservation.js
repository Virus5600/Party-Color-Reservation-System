import React from 'react';
import './Reservation.css';

const Reservation = () =>
  <div className='Reservation'>
    <div className='Reservation-title'>
      <h1>BBQ</h1>
      <p style={
        {
            color: '#FFED4A',
            fontSize: '5vw',
            fontStyle: 'italic',
            textShadow: '2px 2px 4px red'
        }
      }>(including drink all you can)</p>
      <p style={{
        color: '#593A3A',
        fontSize: '10vw',
        fontStyle: 'italic',
        fontWeight: 'bold',
        textShadow: '-1px -1px 0 white, 1px -1px 0 white, -1px 1px 0 white, 1px 1px 0 white'
      }}>2hrs</p>
      <button>RESERVE</button>
    </div>
    <div className='Reservation-description'>
        <Pricing description={'Adult・senior high'} price={3500} />
        <Pricing description={'junior high'} price={2000} />
        <Pricing description={'elementary'} price={1000} />
    </div>
  </div>

const Pricing = ({ description, price }) =>
  <div style={{
    border: '1px solid #941717',
    backgroundColor: '#903C3C',
    paddingTop: '10px',
    paddingBottom: '10px'
  }}>
    <h2 style={{
      color: 'white',
      fontWeight: 'bold',
      fontSize: '5vw',
      padding: '0px',
      margin: '0px'
    }}>{description}</h2>
    <p style={{
      color: 'white',
      fontWeight: 'bold',
      fontSize: '10vw',
      textShadow: '2px 2px 4px black',
      padding: '0px',
      margin: '0px'
    }}>{'¥' + price}</p>
  </div>

export default Reservation;