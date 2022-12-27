import React from 'react';

import Carousel from './carousel/Carousel';
import Reservation from './reservation/Reservation';
import Announcement from './announcement/Announcement';
import AboutUs from './about/aboutus';

const Home = () => (
  <>
    <Carousel />
    <Reservation />
    <Announcement />
    <AboutUs />
  </>
);

export default Home;