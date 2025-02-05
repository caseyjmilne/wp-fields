import React from 'react';
import { HashRouter as Router, Route, Routes } from 'react-router-dom';
import { DndProvider } from 'react-dnd';
import { HTML5Backend } from 'react-dnd-html5-backend'; // Import the backend for DnD
import Dashboard from './components/Dashboard';
import RecordsetList from './components/RecordsetList'; // RecordsetList component with DnD
import RecordsetCreate from './components/RecordsetCreate';
import RecordsetEdit from './components/RecordsetEdit';
import RecordsetDelete from './components/RecordsetDelete';
import RecordsetFields from './components/RecordsetFields';
import './main.css';

function App() {
  return (
    <Router>
      <DndProvider backend={HTML5Backend}> {/* Wrapping the app with DndProvider */}
        <div className="wpf-app">
          <header className="wpf-app-header">
            <h1>WP FIELDS</h1>
          </header>
          <div className="wpf-app-body">
            <nav className="wpf-app-nav">
              <ul className="wpf-app-nav__list">
                <li className="wpf-app-nav__list-item"><a className="wpf-app-nav__link" href="#/">Dashboard</a></li>
                <li className="wpf-app-nav__list-item"><a className="wpf-app-nav__link" href="#/recordsets">Recordsets</a></li>
                <li className="wpf-app-nav__list-item"><a className="wpf-app-nav__link" href="#/recordsets/create">+ Create Recordset</a></li>
                <li className="wpf-app-nav__list-item"><a className="wpf-app-nav__link" href="#">+ Create Fields</a></li>
              </ul>
            </nav>
            <main className="wpf-app-main">
              <Routes>
                {/* Define routes for different screens */}
                <Route path="/" element={<Dashboard />} />
                <Route path="/recordsets" element={<RecordsetList />} />
                <Route path="/recordsets/create" element={<RecordsetCreate />} />
                <Route path="/recordsets/edit/:id" element={<RecordsetEdit />} />
                <Route path="/recordsets/delete/:id" element={<RecordsetDelete />} />
                <Route path="/recordsets/edit/:id/fields" element={<RecordsetFields />} />
              </Routes>
            </main>
          </div>
        </div>
      </DndProvider> {/* Closing DndProvider */}
    </Router>
  );
}

export default App;
