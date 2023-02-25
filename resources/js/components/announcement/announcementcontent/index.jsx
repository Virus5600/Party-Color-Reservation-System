import React from 'react';

import './style.css';

import ReactHtmlParser from 'react-html-parser';

import axios from 'axios';

import { useLoaderData, Link } from 'react-router-dom';


export async function loader({ params }) {

    const API = 'api/react/announcements';

    const response = await axios.get(`${API}/${params.announcementId}`);

    return response.data.announcement;
}


export default function AnnouncementContent() {
    const announcement = useLoaderData();

    return (
        <>
            <div className='fs-3 w-75 mx-auto mt-5 bg-light p-3 rounded-1 text-center'>
                <div>
                    <h1 className='py-0'>
                        "{announcement.title}"
                    </h1>

                    <div className='fs-5 opacity-75'>
                        {announcement.summary}
                    </div>
                    <hr className='dark' />
                    <br />

                    <div className='fs-6 text-start m-4'>

                        {ReactHtmlParser(announcement.content)}
                    </div>
                </div>
            </div>

            <div className=' d-flex justify-content-center mt-2'>
                <Link to='..' relative='path' className="btn btn-primary">Back</Link>
            </div>
        </>
    );
}