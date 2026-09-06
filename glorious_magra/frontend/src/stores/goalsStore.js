import { create } from 'zustand';
import { persist } from 'zustand/middleware';

const defaultGoal = {
  goalType: 'fatLoss',
  targetWeight: '',
  targetFatPercentage: '',
  targetDate: '',
};

export const useGoalStore = create(
  persist(
    (set) => ({
      goal: defaultGoal,
      setGoal: (goal) => set({ goal: { ...defaultGoal, ...goal } }),
      clearGoal: () => set({ goal: defaultGoal }),
    }),
    {
      name: 'gloriousmagra-goal',
    },
  ),
);
