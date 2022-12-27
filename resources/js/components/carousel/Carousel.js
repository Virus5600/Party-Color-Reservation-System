import React from 'react';
import Carousel from 'react-bootstrap/Carousel';
import img01 from './img/img01.png';
import img02 from './img/img02.png';
import img03 from './img/img03.png';
import img04 from './img/img04.png';

const CAROUSEL = () => 
    <Carousel>
      <Carousel.Item>
        <img
          className="d-block w-100"
          src={img01}
          alt="First slide"
        />
      </Carousel.Item>
      <Carousel.Item>
        <img
          className="d-block w-100"
          src={img02}
          alt="Second slide"
        />
      </Carousel.Item>
      <Carousel.Item>
        <img
          className="d-block w-100"
          src={img03}
          alt="Third slide"
        />
      </Carousel.Item>
      {/* <Carousel.Item>
        <img
          className="d-block img-fluid"
          src={img04}
          alt="First slide"
        />
      </Carousel.Item> */}
      {/* <Carousel.Item>
        <img
          className="d-block w-100"
          src="holder.js/800x400?text=Second slide&bg=282c34"
          alt="Second slide"
        />

        <Carousel.Caption>
          <h3>Second slide label</h3>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        </Carousel.Caption>
      </Carousel.Item> */}
    </Carousel>

export default CAROUSEL;