import React from 'react';
import { DollarSign, Users, TrendingUp } from 'lucide-react';

export default function RevenueCard({ revenue }) {
    if (!revenue) return null;

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: revenue.currency || 'USD',
            maximumFractionDigits: 0
        }).format(amount);
    };

    return (
        <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 className="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                <DollarSign className="w-5 h-5 mr-2 text-green-600" />
                Revenue Potential
            </h3>

            <div className="bg-green-50 rounded-lg p-4 mb-6 border border-green-100">
                <p className="text-sm text-green-800 font-medium mb-1">Estimated Annual Revenue</p>
                <div className="text-3xl font-bold text-green-700">
                    {formatCurrency(revenue.min_revenue)} - {formatCurrency(revenue.max_revenue)}
                </div>
            </div>

            <div className="space-y-4">
                <h4 className="text-sm font-medium text-slate-700 uppercase tracking-wider">Key Assumptions</h4>
                
                <div className="flex items-start">
                    <div className="flex-shrink-0">
                        <Users className="w-5 h-5 text-slate-400" />
                    </div>
                    <div className="ml-3">
                        <p className="text-sm font-medium text-slate-900">Target Audience</p>
                        <p className="text-sm text-slate-500">{revenue.assumptions?.target_audience}</p>
                    </div>
                </div>

                <div className="flex items-start">
                    <div className="flex-shrink-0">
                        <TrendingUp className="w-5 h-5 text-slate-400" />
                    </div>
                    <div className="ml-3">
                        <p className="text-sm font-medium text-slate-900">Base User Count</p>
                        <p className="text-sm text-slate-500">{revenue.assumptions?.base_user_count?.toLocaleString()} users</p>
                    </div>
                </div>

                <div className="flex items-start">
                    <div className="flex-shrink-0">
                        <DollarSign className="w-5 h-5 text-slate-400" />
                    </div>
                    <div className="ml-3">
                        <p className="text-sm font-medium text-slate-900">Price Per User</p>
                        <p className="text-sm text-slate-500">{formatCurrency(revenue.assumptions?.price_per_user)} / month</p>
                    </div>
                </div>
            </div>
        </div>
    );
}
