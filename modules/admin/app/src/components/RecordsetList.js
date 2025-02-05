import React, { useState, useEffect } from 'react';
import { useDrag, useDrop } from 'react-dnd';
import { Link } from 'react-router-dom';

const ItemType = 'RECORDSET'; // Defining item type for DnD

const DraggableItem = ({ recordset, index, moveRecordset }) => {
  const [, drag] = useDrag(() => ({
    type: ItemType,
    item: { index },
  }));

  const [, drop] = useDrop(() => ({
    accept: ItemType,
    hover: (item) => {
      if (item.index !== index) {
        moveRecordset(item.index, index);
        item.index = index;
      }
    },
  }));

  return (
    <tr ref={(node) => drag(drop(node))}>
      <td>{recordset.id}</td>
      <td>{recordset.post_type}</td>
      <td>{recordset.position}</td>
      <td>
        <div className="wpf-control-button-group">
          <Link to={`/recordsets/edit/${recordset.id}/fields`} className="wpf-control-button">
            Manage Fields
          </Link>
          <Link to={`/recordsets/edit/${recordset.id}`} className="wpf-control-button">
            Edit
          </Link>
          <Link to={`/recordsets/delete/${recordset.id}`} className="wpf-control-button">
            Delete
          </Link>
        </div>
      </td>
    </tr>
  );
};

function RecordsetList() {
  const [recordsets, setRecordsets] = useState([]);
  const [error, setError] = useState(null);

  useEffect(() => {
    // Fetch the list of field groups from the API
    fetch('http://cjm.local/wp-json/wp-fields/v1/recordsets')
      .then((response) => response.json())
      .then((data) => {
        setRecordsets(data); // Set the fetched data
      })
      .catch(() => setError('Failed to load recordsets'));
  }, []);

  const moveRecordset = (fromIndex, toIndex) => {
    const updatedRecordsets = [...recordsets];
    const [movedRecordset] = updatedRecordsets.splice(fromIndex, 1);
    updatedRecordsets.splice(toIndex, 0, movedRecordset);
    setRecordsets(updatedRecordsets);

    // Optionally update positions in the backend (via API)
    updatedRecordsets.forEach((recordset, index) => {
      fetch(`http://cjm.local/wp-json/wp-fields/v1/recordsets/${recordset.id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': window.wpApiSettings.nonce,
        },
        body: JSON.stringify({ position: index + 1 }),
      });
    });
  };

  if (error) {
    return <div style={{ color: 'red' }}>{error}</div>;
  }

  if (recordsets.length === 0) {
    return <div>No recordsets available.</div>;
  }

  return (
    <div>
      <h2>Field Groups</h2>
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
          {recordsets.map((recordset, index) => (
            <DraggableItem
              key={recordset.id}
              index={index}
              recordset={recordset}
              moveRecordset={moveRecordset}
            />
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default RecordsetList;
