import React, { useEffect, useReducer } from 'react';

// React-Bootstrap
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';

// styling with css
import styles from './Announcement.module.css';

// image components
import foodImg from './img/food.png';

// similar to fetch API
import axios from 'axios';

// to simulate data fetch
const initialAnnouncements = [
  {img: foodImg, date: '2022.1.5', title: 'Halloween 15% Discount Promo', description: 'BBQ & Drinks Plan Adult・senior high: ￥3,500 to ￥ 2,975 BBQ & Drinks Plan Adult・senior high: ￥3,500 to ￥ 2,975 BBQ & Drinks Plan Adult・senior high: ￥3,500 to ￥ 2,975'},
];

const announcementReducer = (currentState, action) => {
  switch (action.type) {
    case 'ANNOUNCEMENT_FETCH_SUCCESS':
      return {
        data: action.payload,
      };
    default:
      throw new Error();
  }
};

// to simulate data fetch
const getAsyncData = () =>
  new Promise(resolve => {
    setTimeout(() => resolve({ data: { announcements: initialAnnouncements }})
    , 2000);
  });

const API_ENDPOINT = '';                                                                                                                                                                                                                                                                                                                                                                                                                                               

const Announcement = () => {
  // state
  const [announcements, dispatchAnnouncements] = useReducer(announcementReducer, { data: [] });
  
  // it is similar to componentDidMount and componentDidUpdate
  useEffect(async () => {
    try {
      const result = await axios.get(API_ENDPOINT);

      dispatchAnnouncements({
        type: 'ANNOUNCEMENT_FETCH_SUCCESS',
        payload: result.data
      });
    } catch {
      throw new Error();
    }
    getAsyncData()
      .then(result => dispatchAnnouncements({
        type: 'ANNOUNCEMENT_FETCH_SUCCESS',
        payload: result.data.announcements,
      }))
      .catch(() => new Error());
    }, []
  );

  return (
    <>
      <div className={styles.AnnouncementTitle}>
        <h1>ANNOUNCEMENT</h1>
      </div>
      <div className={styles.AnnouncementList}>
        { announcements.data.map(item => <AnnouncementItem {...item} />) }
      </div>
    </>
  );
};

const AnnouncementItem = ({ img, date, title, description }) => (
  <Container fluid className={styles.AnnouncementItem} >
    <Row>
      <Col>
        <div className={styles.image}>
          <img src={img} alt='food' />
        </div>
      </Col>
      <Col>
        <span>{date}</span>
        <h2>{title}</h2>
        <p>{description}</p>
      </Col>
    </Row>
  </Container>
);

export default Announcement;