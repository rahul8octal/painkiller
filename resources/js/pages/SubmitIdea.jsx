import React, { useState } from 'react';
import axios from 'axios';
import { Send, Loader2, CheckCircle } from 'lucide-react';

export default function SubmitIdea() {
    const [formData, setFormData] = useState({
        title: '',
        description: '',
        email: ''
    });
    const [status, setStatus] = useState('idle'); // idle, submitting, success, error
    const [error, setError] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        setStatus('submitting');
        setError('');

        try {
            await axios.post('/api/submit-idea', formData);
            setStatus('success');
            setFormData({ title: '', description: '', email: '' });
        } catch (err) {
            setStatus('error');
            setError(err.response?.data?.message || 'Something went wrong. Please try again.');
        }
    };

    return (
        <div className="max-w-2xl mx-auto">
            <div className="text-center mb-10">
                <h1 className="text-3xl font-bold text-slate-900">Analyze Your Startup Idea</h1>
                <p className="mt-3 text-lg text-slate-600">
                    Get a comprehensive AI report on market validation, revenue potential, and execution strategy.
                </p>
            </div>

            <div className="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                {status === 'success' ? (
                    <div className="text-center py-10">
                        <div className="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                            <CheckCircle className="h-8 w-8 text-green-600" />
                        </div>
                        <h2 className="text-2xl font-bold text-slate-900 mb-2">Idea Submitted!</h2>
                        <p className="text-slate-600 mb-6">
                            We are analyzing your idea. This usually takes 2-3 minutes. Check the database shortly.
                        </p>
                        <button 
                            onClick={() => setStatus('idle')}
                            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Analyze Another Idea
                        </button>
                    </div>
                ) : (
                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div>
                            <label htmlFor="title" className="block text-sm font-medium text-slate-700">
                                Idea Title <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="title"
                                required
                                className="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2 border"
                                placeholder="e.g., Uber for Dog Walking"
                                value={formData.title}
                                onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                            />
                        </div>

                        <div>
                            <label htmlFor="description" className="block text-sm font-medium text-slate-700">
                                Problem & Solution Description <span className="text-red-500">*</span>
                            </label>
                            <div className="mt-1">
                                <textarea
                                    id="description"
                                    rows={5}
                                    required
                                    className="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2 border"
                                    placeholder="Describe the problem you are solving and your proposed solution..."
                                    value={formData.description}
                                    onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                                />
                            </div>
                            <p className="mt-2 text-sm text-slate-500">
                                Be as specific as possible for better analysis results.
                            </p>
                        </div>

                        <div>
                            <label htmlFor="email" className="block text-sm font-medium text-slate-700">
                                Email Address (Optional)
                            </label>
                            <input
                                type="email"
                                id="email"
                                className="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2 border"
                                placeholder="you@example.com"
                                value={formData.email}
                                onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                            />
                        </div>

                        {status === 'error' && (
                            <div className="rounded-md bg-red-50 p-4">
                                <div className="flex">
                                    <div className="ml-3">
                                        <h3 className="text-sm font-medium text-red-800">Error</h3>
                                        <div className="mt-2 text-sm text-red-700">
                                            <p>{error}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        <button
                            type="submit"
                            disabled={status === 'submitting'}
                            className="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {status === 'submitting' ? (
                                <>
                                    <Loader2 className="animate-spin -ml-1 mr-2 h-4 w-4" />
                                    Analyzing...
                                </>
                            ) : (
                                <>
                                    <Send className="-ml-1 mr-2 h-4 w-4" />
                                    Generate Report
                                </>
                            )}
                        </button>
                    </form>
                )}
            </div>
        </div>
    );
}
