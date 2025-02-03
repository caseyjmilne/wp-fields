// src/components/ListRecordsets.js
import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';

function ListRecordsets() {
  const [recordsets, setRecordsets] = useState([]);
  const [error, setError] = useState(null);

  useEffect(() => {
    // Fetch the list of recordsets from the API
    fetch('http://cjm.local/wp-json/wp-fields/v1/recordsets')
      .then((response) => response.json())
      .then((data) => {
        setRecordsets(data); // Set the fetched data
      })
      .catch(() => setError('Failed to load recordsets'));
  }, []);

  const handleDelete = (id) => {
    if (window.confirm('Are you sure you want to delete this recordset?')) {
      // Perform the DELETE request to the API
      fetch(`http://cjm.local/wp-json/wp-fields/v1/recordsets/${id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then((response) => response.json())
        .then((data) => {
          // Remove the deleted recordset from the list
          setRecordsets((prevRecordsets) =>
            prevRecordsets.filter((recordset) => recordset.id !== id)
          );
        })
        .catch(() => setError('Failed to delete recordset'));
    }
  };

  if (error) {
    return <div style={{ color: 'red' }}>{error}</div>;
  }

  if (recordsets.length === 0) {
    return <div>No recordsets available.</div>;
  }

  return (
    <div>
      <h2>Recordsets List</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Post Type</th>
            <th>Position</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {recordsets.map((recordset) => (
            <tr key={recordset.id}>
              <td>{recordset.id}</td>
              <td>{recordset.post_type}</td>
              <td>{recordset.position}</td>
              <td>
                {/* Edit Link */}
                <Link to={`/recordsets/edit/${recordset.id}`} className="edit-link">
                  Edit
                </Link>
                &nbsp;|&nbsp;
                {/* Delete Button */}
                <button
                  onClick={() => handleDelete(recordset.id)}
                  className="delete-btn"
                  style={{ color: 'red' }}
                >
                  Delete
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default ListRecordsets;
