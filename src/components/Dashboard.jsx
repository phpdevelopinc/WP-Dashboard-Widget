import React, { useEffect, useState } from 'react';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';

const Dashboard = (props) => {
  const [posts, setPosts] = useState([]);
    useEffect(() => {
        async function loadPosts() {
            const response = await fetch('/wp-json/chardata/v1/data?filter='+props.filter);
            if(!response.ok) {
                return;
            }
    
            const posts = await response.json();
            setPosts(posts);
        }
        
        loadPosts();

   }, [])
    return (
        <div className="line-chart-wrapper">
            {posts.length > 0 ?(
            <LineChart width={450} height={300} data={posts}>
                <CartesianGrid strokeDasharray="3 3"/>
                <XAxis dataKey={Object.keys(posts[0])[0]} />
                <YAxis />
                <Tooltip />
                <Legend />
                <Line type="monotone" dataKey="pv" stroke="#8884d8" />
                <Line type="monotone" dataKey="uv" stroke="#82ca9d" />
            </LineChart>
            ): (
              <h2>Loading...</h2>
            )}
        </div>
     );
}

export default Dashboard;
