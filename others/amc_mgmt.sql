

--
-- Database: `amc_mgmt`
--

-- --------------------------------------------------------

--
-- Table structure for table `agent`
--

CREATE TABLE `agent` (
  `id` int(11) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(24) NOT NULL,
  `updated_by` int(24) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `agent_nm` varchar(128) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pass_code` varchar(255) NOT NULL,
  `area` varchar(128) NOT NULL,
  `pincode` int(8) NOT NULL,
  `city` varchar(128) NOT NULL,
  `address_ln` text NOT NULL,
  `primary_contact` varchar(16) NOT NULL,
  `secondary_contact` varchar(16) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `cust_mstr`
--

CREATE TABLE `cust_mstr` (
  `id` int(11) NOT NULL,
  `customer_nm` varchar(255) DEFAULT NULL,
  `company_nm` varchar(255) NOT NULL,
  `address_ln` varchar(400) DEFAULT NULL,
  `contact` varchar(24) NOT NULL,
  `telephone` varchar(12) DEFAULT NULL,
  `updated_by` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `amc_st_date` date DEFAULT NULL,
  `amc_end_date` date DEFAULT NULL,
  `tally_st_date` date DEFAULT NULL,
  `tally_end_date` date DEFAULT NULL,
  `cloud_st_date` date DEFAULT NULL,
  `cloud_end_date` date DEFAULT NULL,
  `spl_cust_note` text DEFAULT NULL,
  `license_typ` varchar(24) DEFAULT NULL,
  `auditor` varchar(300) DEFAULT NULL,
  `referredBy` varchar(300) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `sys_email` varchar(255) DEFAULT NULL,
  `pincode` int(11) NOT NULL,
  `city` varchar(255) NOT NULL,
  `area` varchar(255) DEFAULT NULL,
  `service_type` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `customer_uniq_code` varchar(56) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `lead_tracker`
--

CREATE TABLE `lead_tracker` (
  `id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NULL DEFAULT NULL,
  `created_by` int(24) NOT NULL,
  `lead_status` varchar(24) NOT NULL,
  `notes` text DEFAULT NULL,
  `follow_up_dt` date NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  `lead_nm` varchar(255) NOT NULL,
  `contact` varchar(16) NOT NULL,
  `company_nm` varchar(128) DEFAULT NULL,
  `requirement` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `address_ln` text DEFAULT NULL,
  `pincode` int(11) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `req_tracker`
--

CREATE TABLE `req_tracker` (
  `id` int(11) NOT NULL,
  `brief` varchar(300) NOT NULL,
  `detailed` text DEFAULT NULL,
  `cust_id` int(12) DEFAULT NULL,
  `phone` varchar(24) NOT NULL,
  `nm` varchar(128) NOT NULL,
  `requirement_status` varchar(128) NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(24) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(24) NOT NULL,
  `is_active` int(11) NOT NULL,
  `is_deleted` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(255) NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(12) NOT NULL,
  `assignd_agent_id` int(24) NOT NULL,
  `customer_id` int(24) NOT NULL,
  `comments` text NOT NULL,
  `problem_desc` text NOT NULL,
  `new_requirement` varchar(255) DEFAULT NULL,
  `status` varchar(64) NOT NULL,
  `problem_stmt` varchar(255) NOT NULL,
  `service_typ` varchar(64) NOT NULL,
  `service_thru` varchar(64) NOT NULL,
  `is_under_amc` tinyint(1) NOT NULL,
  `solved_by` int(24) NOT NULL,
  `notes` text DEFAULT NULL,
  `closed_date` timestamp NULL DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  `uniq_id` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `usr`
--

CREATE TABLE `usr` (
  `id` int(11) NOT NULL,
  `nm` varchar(128) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Indexes for table `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cust_mstr`
--
ALTER TABLE `cust_mstr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lead_tracker`
--
ALTER TABLE `lead_tracker`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `req_tracker`
--
ALTER TABLE `req_tracker`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usr`
--
ALTER TABLE `usr`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agent`
--
ALTER TABLE `agent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cust_mstr`
--
ALTER TABLE `cust_mstr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `lead_tracker`
--
ALTER TABLE `lead_tracker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `req_tracker`
--
ALTER TABLE `req_tracker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `usr`
--
ALTER TABLE `usr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
