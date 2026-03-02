-- =============================================
-- COMPLAINT MANAGEMENT SYSTEM — SUPABASE SCHEMA
-- Run this entire file in Supabase SQL Editor
-- =============================================

-- Enable UUID extension
create extension if not exists "uuid-ossp";

-- =============================================
-- PROFILES (extends Supabase auth.users)
-- =============================================
create table public.profiles (
  id uuid references auth.users(id) on delete cascade primary key,
  name text not null,
  email text not null,
  role text not null default 'respondent' check (role in ('admin','respondent','lawyer','cast_member')),
  created_at timestamptz default now(),
  updated_at timestamptz default now()
);

-- =============================================
-- LOCATIONS
-- =============================================
create table public.locations (
  id uuid default uuid_generate_v4() primary key,
  name text not null,
  city text,
  state text,
  address text,
  created_at timestamptz default now(),
  updated_at timestamptz default now()
);

-- =============================================
-- STAGES
-- =============================================
create table public.stages (
  id uuid default uuid_generate_v4() primary key,
  name text not null,
  step_number int not null default 1,
  color text default '#3b82f6',
  is_active boolean default true,
  created_at timestamptz default now(),
  updated_at timestamptz default now()
);

-- Default stages
insert into public.stages (name, step_number, color) values
  ('Initial Review', 1, '#3b82f6'),
  ('Investigation', 2, '#f59e0b'),
  ('Resolution', 3, '#10b981'),
  ('Closed', 4, '#6b7280');

-- =============================================
-- COMPLAINTS
-- =============================================
create table public.complaints (
  id uuid default uuid_generate_v4() primary key,
  case_number text unique not null,
  submitted_as text default 'individual' check (submitted_as in ('individual','organization')),
  name text,
  email text,
  phone_number text,
  description text not null,
  location_id uuid references public.locations(id) on delete set null,
  complaint_type text,
  complaint_about text,
  complainee_name text,
  complainee_email text,
  complainee_address text,
  witnesses text,
  evidence_type text,
  evidence_description text,
  status text default 'submitted' check (status in ('submitted','under_review','escalated','resolved','closed')),
  date_of_experience date,
  anonymity boolean default false,
  stage_id uuid references public.stages(id) on delete set null,
  user_id uuid references public.profiles(id) on delete set null,
  submitted_by_admin boolean default false,
  submitted_by_admin_id uuid references public.profiles(id) on delete set null,
  created_at timestamptz default now(),
  updated_at timestamptz default now()
);

-- =============================================
-- COMPLAINT RESPONDENTS
-- =============================================
create table public.complaint_respondents (
  id uuid default uuid_generate_v4() primary key,
  complaint_id uuid references public.complaints(id) on delete cascade not null,
  user_id uuid references public.profiles(id) on delete cascade not null,
  input text,
  responded_at timestamptz,
  created_at timestamptz default now(),
  unique(complaint_id, user_id)
);

-- =============================================
-- COMPLAINT LAWYERS
-- =============================================
create table public.complaint_lawyers (
  id uuid default uuid_generate_v4() primary key,
  complaint_id uuid references public.complaints(id) on delete cascade not null,
  user_id uuid references public.profiles(id) on delete cascade not null,
  input text,
  responded_at timestamptz,
  created_at timestamptz default now(),
  unique(complaint_id, user_id)
);

-- =============================================
-- COMPLAINT RESPONSES (from respondents)
-- =============================================
create table public.complaint_responses (
  id uuid default uuid_generate_v4() primary key,
  complaint_respondent_id uuid references public.complaint_respondents(id) on delete cascade not null,
  response text not null,
  created_at timestamptz default now(),
  updated_at timestamptz default now()
);

-- =============================================
-- COMPLAINT REPLIES (messages within complaint)
-- =============================================
create table public.complaint_replies (
  id uuid default uuid_generate_v4() primary key,
  complaint_id uuid references public.complaints(id) on delete cascade not null,
  user_id uuid references public.profiles(id) on delete set null,
  message text not null,
  recipient_id uuid references public.profiles(id) on delete set null,
  recipient_type text check (recipient_type in ('respondent','lawyer','admin')),
  created_at timestamptz default now()
);

-- =============================================
-- ATTACHMENTS
-- =============================================
create table public.attachments (
  id uuid default uuid_generate_v4() primary key,
  complaint_id uuid references public.complaints(id) on delete cascade not null,
  respondent_response_id uuid references public.complaint_responses(id) on delete set null,
  uploaded_by uuid references public.profiles(id) on delete set null,
  file_path text not null,
  file_name text,
  file_type text,
  file_size bigint,
  description text,
  type text default 'evidence' check (type in ('evidence','response')),
  created_at timestamptz default now()
);

-- =============================================
-- INVESTIGATION LOGS
-- =============================================
create table public.investigation_logs (
  id uuid default uuid_generate_v4() primary key,
  complaint_id uuid references public.complaints(id) on delete cascade not null,
  note text not null,
  next_steps text,
  created_by uuid references public.profiles(id) on delete set null,
  created_at timestamptz default now(),
  updated_at timestamptz default now()
);

-- =============================================
-- CASE RESOLUTIONS
-- =============================================
create table public.case_resolutions (
  id uuid default uuid_generate_v4() primary key,
  complaint_id uuid references public.complaints(id) on delete cascade not null,
  resolution_text text not null,
  generated_pdf_path text,
  template_type text default 'standard' check (template_type in ('standard','detailed','summary')),
  generated_by uuid references public.profiles(id) on delete set null,
  created_at timestamptz default now(),
  updated_at timestamptz default now()
);

-- =============================================
-- CASE SIGNATURES
-- =============================================
create table public.case_signatures (
  id uuid default uuid_generate_v4() primary key,
  complaint_id uuid references public.complaints(id) on delete cascade not null,
  resolution_id uuid references public.case_resolutions(id) on delete cascade not null,
  user_id uuid references public.profiles(id) on delete set null,
  signer_name text not null,
  signer_email text not null,
  signer_role text check (signer_role in ('complainant','respondent','leadership')),
  signature_path text,
  signed_at timestamptz,
  ip_address text,
  created_at timestamptz default now(),
  unique(resolution_id, signer_email)
);

-- =============================================
-- RESPONDENT ACCESS TOKENS
-- =============================================
create table public.respondent_accesses (
  id uuid default uuid_generate_v4() primary key,
  user_id uuid references public.profiles(id) on delete cascade not null,
  complaint_id uuid references public.complaints(id) on delete cascade not null,
  access_token text unique not null,
  access_type text default 'respondent' check (access_type in ('respondent','lawyer')),
  expires_at timestamptz,
  last_accessed_at timestamptz,
  created_at timestamptz default now()
);

-- =============================================
-- LAWYER ACCESS TOKENS
-- =============================================
create table public.lawyer_accesses (
  id uuid default uuid_generate_v4() primary key,
  user_id uuid references public.profiles(id) on delete cascade not null,
  complaint_id uuid references public.complaints(id) on delete cascade not null,
  access_token text unique not null,
  expires_at timestamptz,
  last_accessed_at timestamptz,
  created_at timestamptz default now()
);

-- =============================================
-- GUEST OTPs
-- =============================================
create table public.guest_otps (
  id uuid default uuid_generate_v4() primary key,
  email text not null,
  case_number text not null,
  otp text not null,
  expires_at timestamptz not null,
  verified boolean default false,
  created_at timestamptz default now()
);

-- =============================================
-- STAGE CHANGE LOGS
-- =============================================
create table public.stage_change_logs (
  id uuid default uuid_generate_v4() primary key,
  complaint_id uuid references public.complaints(id) on delete cascade not null,
  from_stage_id uuid references public.stages(id) on delete set null,
  to_stage_id uuid references public.stages(id) on delete set null,
  changed_by uuid references public.profiles(id) on delete set null,
  note text,
  created_at timestamptz default now()
);

-- =============================================
-- ROW LEVEL SECURITY
-- =============================================

alter table public.profiles enable row level security;
alter table public.complaints enable row level security;
alter table public.stages enable row level security;
alter table public.locations enable row level security;
alter table public.complaint_respondents enable row level security;
alter table public.complaint_lawyers enable row level security;
alter table public.complaint_responses enable row level security;
alter table public.complaint_replies enable row level security;
alter table public.attachments enable row level security;
alter table public.investigation_logs enable row level security;
alter table public.case_resolutions enable row level security;
alter table public.case_signatures enable row level security;
alter table public.respondent_accesses enable row level security;
alter table public.lawyer_accesses enable row level security;
alter table public.guest_otps enable row level security;
alter table public.stage_change_logs enable row level security;

-- Helper function: get current user role
create or replace function public.get_my_role()
returns text language sql security definer as $$
  select role from public.profiles where id = auth.uid();
$$;

-- PROFILES policies
create policy "Users can view own profile" on public.profiles
  for select using (id = auth.uid());
create policy "Admins can view all profiles" on public.profiles
  for select using (public.get_my_role() = 'admin');
create policy "Users can update own profile" on public.profiles
  for update using (id = auth.uid());
create policy "Admins can manage profiles" on public.profiles
  for all using (public.get_my_role() = 'admin');

-- COMPLAINTS policies
create policy "Admins see all complaints" on public.complaints
  for all using (public.get_my_role() = 'admin');
create policy "Cast members see all complaints" on public.complaints
  for select using (public.get_my_role() = 'cast_member');
create policy "Users see own complaints" on public.complaints
  for select using (user_id = auth.uid());
create policy "Authenticated users can create complaints" on public.complaints
  for insert with check (auth.uid() is not null);
create policy "Public can insert guest complaints" on public.complaints
  for insert with check (true);
create policy "Public can view complaints by case_number" on public.complaints
  for select using (true);

-- STAGES policies (read by all, write by admin)
create policy "Anyone can view stages" on public.stages
  for select using (true);
create policy "Admins manage stages" on public.stages
  for all using (public.get_my_role() = 'admin');

-- LOCATIONS policies
create policy "Anyone can view locations" on public.locations
  for select using (true);
create policy "Admins manage locations" on public.locations
  for all using (public.get_my_role() = 'admin');

-- COMPLAINT RESPONDENTS
create policy "Admins manage respondents" on public.complaint_respondents
  for all using (public.get_my_role() = 'admin');
create policy "Respondents see own assignments" on public.complaint_respondents
  for select using (user_id = auth.uid());

-- COMPLAINT LAWYERS
create policy "Admins manage lawyers" on public.complaint_lawyers
  for all using (public.get_my_role() = 'admin');
create policy "Lawyers see own assignments" on public.complaint_lawyers
  for select using (user_id = auth.uid());

-- COMPLAINT RESPONSES
create policy "Admins see all responses" on public.complaint_responses
  for all using (public.get_my_role() = 'admin');
create policy "Respondents manage own responses" on public.complaint_responses
  for all using (
    exists (
      select 1 from public.complaint_respondents cr
      where cr.id = complaint_respondent_id and cr.user_id = auth.uid()
    )
  );

-- COMPLAINT REPLIES
create policy "Admins manage replies" on public.complaint_replies
  for all using (public.get_my_role() = 'admin');
create policy "Users see own complaint replies" on public.complaint_replies
  for select using (
    user_id = auth.uid() or recipient_id = auth.uid()
  );
create policy "Authenticated users can reply" on public.complaint_replies
  for insert with check (auth.uid() is not null);

-- ATTACHMENTS
create policy "Admins manage attachments" on public.attachments
  for all using (public.get_my_role() = 'admin');
create policy "Users see attachments for own complaints" on public.attachments
  for select using (
    exists (
      select 1 from public.complaints c
      where c.id = complaint_id and (c.user_id = auth.uid() or public.get_my_role() in ('cast_member'))
    )
  );
create policy "Authenticated users can upload" on public.attachments
  for insert with check (auth.uid() is not null);

-- INVESTIGATION LOGS
create policy "Admins manage logs" on public.investigation_logs
  for all using (public.get_my_role() = 'admin');
create policy "Cast members view logs" on public.investigation_logs
  for select using (public.get_my_role() = 'cast_member');

-- CASE RESOLUTIONS
create policy "Admins manage resolutions" on public.case_resolutions
  for all using (public.get_my_role() = 'admin');
create policy "Anyone can view resolutions" on public.case_resolutions
  for select using (true);

-- CASE SIGNATURES
create policy "Admins manage signatures" on public.case_signatures
  for all using (public.get_my_role() = 'admin');
create policy "Anyone can insert signature" on public.case_signatures
  for insert with check (true);
create policy "Anyone can view signatures" on public.case_signatures
  for select using (true);

-- RESPONDENT ACCESSES
create policy "Admins manage respondent access" on public.respondent_accesses
  for all using (public.get_my_role() = 'admin');
create policy "Respondents see own access" on public.respondent_accesses
  for select using (user_id = auth.uid());

-- LAWYER ACCESSES
create policy "Admins manage lawyer access" on public.lawyer_accesses
  for all using (public.get_my_role() = 'admin');
create policy "Lawyers see own access" on public.lawyer_accesses
  for select using (user_id = auth.uid());

-- GUEST OTPS
create policy "Public can insert guest otp" on public.guest_otps
  for insert with check (true);
create policy "Public can view guest otp" on public.guest_otps
  for select using (true);
create policy "Public can update guest otp" on public.guest_otps
  for update using (true);

-- STAGE CHANGE LOGS
create policy "Admins manage stage logs" on public.stage_change_logs
  for all using (public.get_my_role() = 'admin');
create policy "Anyone can view stage logs" on public.stage_change_logs
  for select using (true);

-- =============================================
-- AUTO-CREATE PROFILE ON SIGNUP
-- =============================================
create or replace function public.handle_new_user()
returns trigger language plpgsql security definer as $$
begin
  insert into public.profiles (id, name, email, role)
  values (
    new.id,
    coalesce(new.raw_user_meta_data->>'name', split_part(new.email, '@', 1)),
    new.email,
    coalesce(new.raw_user_meta_data->>'role', 'respondent')
  );
  return new;
end;
$$;

create trigger on_auth_user_created
  after insert on auth.users
  for each row execute function public.handle_new_user();

-- =============================================
-- STORAGE BUCKETS
-- =============================================
insert into storage.buckets (id, name, public) values ('complaint-attachments', 'complaint-attachments', true);
insert into storage.buckets (id, name, public) values ('signatures', 'signatures', true);

create policy "Public can upload attachments" on storage.objects
  for insert with check (bucket_id = 'complaint-attachments');
create policy "Public can view attachments" on storage.objects
  for select using (bucket_id = 'complaint-attachments');
create policy "Admins can delete attachments" on storage.objects
  for delete using (bucket_id = 'complaint-attachments' and public.get_my_role() = 'admin');

create policy "Public can upload signatures" on storage.objects
  for insert with check (bucket_id = 'signatures');
create policy "Public can view signatures" on storage.objects
  for select using (bucket_id = 'signatures');
