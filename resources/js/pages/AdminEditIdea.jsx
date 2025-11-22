import React, { useEffect, useState } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import axios from 'axios';
import { Save, ArrowLeft, Loader2 } from 'lucide-react';

export default function AdminEditIdea() {
    const { id } = useParams();
    const navigate = useNavigate();
    const isNew = !id;

    const [formData, setFormData] = useState({
        title: '',
        description: '',
        status: 'pending',
        revenue_potential: '{}',
        market_validation: '{}',
        creative_assets: '{}',
    });
    const [loading, setLoading] = useState(!isNew);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState('');

    useEffect(() => {
        if (!isNew) {
            axios.get(`/api/admin/ideas/${id}`)
                .then(response => {
                    const idea = response.data;
                    setFormData({
                        title: idea.problem?.title || '',
                        description: idea.problem?.body || '',
                        status: idea.review_status || 'pending',
                        revenue_potential: JSON.stringify(idea.revenue_potential || {}, null, 2),
                        market_validation: JSON.stringify(idea.market_validation || {}, null, 2),
                        creative_assets: JSON.stringify(idea.creative_assets || {}, null, 2),
                    });
                    setLoading(false);
                })
                .catch(err => {
                    console.error('Error fetching idea:', err);
                    setError('Failed to load idea.');
                    setLoading(false);
                });
        }
    }, [id, isNew]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        setError('');

        try {
            const payload = {
                title: formData.title,
                description: formData.description,
                status: formData.status,
                revenue_potential: JSON.parse(formData.revenue_potential),
                market_validation: JSON.parse(formData.market_validation),
                creative_assets: JSON.parse(formData.creative_assets),
            };

            if (isNew) {
                await axios.post('/api/admin/ideas', payload);
            } else {
                await axios.put(`/api/admin/ideas/${id}`, payload);
            }
            navigate('/admin');
        } catch (err) {
            console.error('Error saving idea:', err);
            setError(err.response?.data?.message || 'Failed to save idea. Check JSON format.');
            setSaving(false);
        }
    };

    if (loading) {
        return (
            <div className="flex justify-center items-center h-64">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            </div>
        );
    }

    return (
        <div className="max-w-4xl mx-auto">
            <div className="flex items-center mb-6">
                <Link to="/admin" className="mr-4 text-slate-500 hover:text-slate-700">
                    <ArrowLeft className="w-6 h-6" />
                </Link>
                <h1 className="text-2xl font-bold text-slate-900">
                    {isNew ? 'Add New Idea' : 'Edit Idea'}
                </h1>
            </div>

            <form onSubmit={handleSubmit} className="bg-white rounded-xl shadow-sm border border-slate-200 p-8 space-y-6">
                {error && (
                    <div className="bg-red-50 border-l-4 border-red-400 p-4">
                        <div className="flex">
                            <div className="ml-3">
                                <p className="text-sm text-red-700">{error}</p>
                            </div>
                        </div>
                    </div>
                )}

                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div className="col-span-2">
                        <label className="block text-sm font-medium text-slate-700">Title</label>
                        <input
                            type="text"
                            required
                            value={formData.title}
                            onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                            className="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2 border"
                        />
                    </div>

                    <div className="col-span-2">
                        <label className="block text-sm font-medium text-slate-700">Description</label>
                        <textarea
                            rows={4}
                            required
                            value={formData.description}
                            onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                            className="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2 border"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700">Status</label>
                        <select
                            value={formData.status}
                            onChange={(e) => setFormData({ ...formData, status: e.target.value })}
                            className="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2 border"
                        >
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div className="border-t border-slate-200 pt-6">
                    <h3 className="text-lg font-medium text-slate-900 mb-4">Advanced Data (JSON)</h3>
                    
                    <div className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-slate-700">Revenue Potential</label>
                            <textarea
                                rows={5}
                                value={formData.revenue_potential}
                                onChange={(e) => setFormData({ ...formData, revenue_potential: e.target.value })}
                                className="mt-1 block w-full font-mono text-xs rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 border"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700">Market Validation</label>
                            <textarea
                                rows={5}
                                value={formData.market_validation}
                                onChange={(e) => setFormData({ ...formData, market_validation: e.target.value })}
                                className="mt-1 block w-full font-mono text-xs rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 border"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700">Creative Assets</label>
                            <textarea
                                rows={5}
                                value={formData.creative_assets}
                                onChange={(e) => setFormData({ ...formData, creative_assets: e.target.value })}
                                className="mt-1 block w-full font-mono text-xs rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 border"
                            />
                        </div>
                    </div>
                </div>

                <div className="flex justify-end">
                    <button
                        type="submit"
                        disabled={saving}
                        className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                        {saving ? (
                            <>
                                <Loader2 className="animate-spin -ml-1 mr-2 h-4 w-4" />
                                Saving...
                            </>
                        ) : (
                            <>
                                <Save className="-ml-1 mr-2 h-4 w-4" />
                                Save Idea
                            </>
                        )}
                    </button>
                </div>
            </form>
        </div>
    );
}
