// src/components/ListRecordsets.js
import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';

function RecordsetList() {
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
                {/* Manage Link */}
                <Link to={`/recordsets/edit/${recordset.id}/fields`} className="edit-link">
                  Manage Fields
                </Link>
                {/* Edit Link */}
                <Link to={`/recordsets/edit/${recordset.id}`} className="edit-link">
                  Edit
                </Link>
                &nbsp;|&nbsp;
                {/* Delete Confirmation Link */}
                <Link to={`/recordsets/delete/${recordset.id}`} className="delete-link">
                  Delete
                </Link>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default RecordsetList;
