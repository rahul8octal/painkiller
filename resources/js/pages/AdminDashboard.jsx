import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import { Edit, Trash2, CheckCircle, XCircle, Plus, Filter } from 'lucide-react';

export default function AdminDashboard() {
    const [ideas, setIdeas] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('all');

    const fetchIdeas = () => {
        setLoading(true);
        axios.get(`/api/admin/ideas?status=${filter}`)
            .then(response => {
                setIdeas(response.data.data);
                setLoading(false);
            })
            .catch(error => {
                console.error('Error fetching ideas:', error);
                setLoading(false);
            });
    };

    useEffect(() => {
        fetchIdeas();
    }, [filter]);

    const handleStatusChange = (id, newStatus) => {
        const endpoint = newStatus === 'approved' ? 'approve' : 'reject';
        axios.post(`/api/admin/ideas/${id}/${endpoint}`)
            .then(() => {
                fetchIdeas(); // Refresh list
            })
            .catch(error => console.error(`Error ${endpoint} idea:`, error));
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this idea? This action cannot be undone.')) {
            axios.delete(`/api/admin/ideas/${id}`)
                .then(() => {
                    fetchIdeas();
                })
                .catch(error => console.error('Error deleting idea:', error));
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className="text-3xl font-bold text-slate-900">Admin Dashboard</h1>
                <Link to="/admin/ideas/new" className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <Plus className="w-4 h-4 mr-2" />
                    Add New Idea
                </Link>
            </div>

            <div className="bg-white rounded-lg shadow border border-slate-200 overflow-hidden">
                <div className="p-4 border-b border-slate-200 bg-slate-50 flex items-center space-x-4">
                    <Filter className="w-4 h-4 text-slate-500" />
                    <span className="text-sm font-medium text-slate-700">Filter Status:</span>
                    <select 
                        value={filter} 
                        onChange={(e) => setFilter(e.target.value)}
                        className="rounded-md border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="all">All Ideas</option>
                        <option value="pending">Pending Review</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                {loading ? (
                    <div className="p-10 text-center">
                        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                    </div>
                ) : (
                    <table className="min-w-full divide-y divide-slate-200">
                        <thead className="bg-slate-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Title</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Score</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-slate-200">
                            {ideas.map((idea) => (
                                <tr key={idea.id} className="hover:bg-slate-50">
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-slate-500">#{idea.id}</td>
                                    <td className="px-6 py-4">
                                        <div className="text-sm font-medium text-slate-900 line-clamp-1">{idea.problem?.title || 'Untitled'}</div>
                                        <div className="text-xs text-slate-500 line-clamp-1">{idea.problem?.body}</div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                            idea.review_status === 'approved' ? 'bg-green-100 text-green-800' :
                                            idea.review_status === 'rejected' ? 'bg-red-100 text-red-800' :
                                            'bg-yellow-100 text-yellow-800'
                                        }`}>
                                            {idea.review_status || 'Pending'}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        {Math.round(idea.total_score || 0)}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <Link to={`/admin/ideas/${idea.id}/edit`} className="text-indigo-600 hover:text-indigo-900" title="Edit">
                                            <Edit className="w-4 h-4 inline" />
                                        </Link>
                                        {idea.review_status !== 'approved' && (
                                            <button onClick={() => handleStatusChange(idea.id, 'approved')} className="text-green-600 hover:text-green-900" title="Approve">
                                                <CheckCircle className="w-4 h-4 inline" />
                                            </button>
                                        )}
                                        {idea.review_status !== 'rejected' && (
                                            <button onClick={() => handleStatusChange(idea.id, 'rejected')} className="text-red-600 hover:text-red-900" title="Reject">
                                                <XCircle className="w-4 h-4 inline" />
                                            </button>
                                        )}
                                        <button onClick={() => handleDelete(idea.id)} className="text-slate-400 hover:text-red-600" title="Delete">
                                            <Trash2 className="w-4 h-4 inline" />
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>
        </div>
    );
}
