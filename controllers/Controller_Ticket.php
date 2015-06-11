<?php
	class Controller_Ticket extends Controller
	{
		public $f3,$mybb,$mybbi,$db;
		
		// Submit A Ticket
		public function submit()
		{	
			parent::index();
			/*
				This part is for submitting a ticket
			*/
			//calling the js that is responsible for the custom combobox
			$this->f3->set('rolesComboboxFunction', '<script type="text/javascript"> rolesCombobox(); </script>');
			//calling the  js that is responsible for adding another ticket at the same page
			$this->f3->set('anotherTicket', '<script type="text/javascript"> addAnotherTicket('.json_encode(Model_Ticket::getRoles()).'); </script>');
			//calling the  js that is responsible for delete an already added ticket from a page by clicking x
			$this->f3->set('deleteTicket', '<script type="text/javascript"> deleteChild(); </script>');
			//calling the cancel submit
			$this->f3->set('cancel', '<script type="text/javascript"> cancelsubmit(); </script>');
			
			
			$this->f3->set('main','views/ticket/submitTicket.html');
			
			//here I tell that I want to execute the submitTicket function that is existed in Model_Ticket.php
			if (isset($_POST) && !empty($_POST))
			{
				Model_Ticket::submitTicket();
			}
			
			// Set Page Name
			$this->config('page_name','Submit a Ticket');
			
			// Output Layout
			echo View::instance()->render('views/layout.html');
		}
		
		// View Tickets
		public function manage($canAdmin)
		{
			if(!empty($_POST['ticket_id']))
			{
				echo Model_Ticket::bringTicketHistory($_POST['ticket_id']);
			}
			else
			{			
				//Bridge::print_r2($_POST);
				parent::index();
				/*
					This part is for viewing a ticket
				*/
				if(isset($_POST['save'])) 
				{	
					//Bridge::print_r2($_POST);
					Model_Ticket::updatePost($_POST['id'], $_POST['desc'], $_POST['status'], $_POST['prior']);
				}
				if(isset($_POST['filter_date']))
				{
					//Bridge::print_r2($_POST);
					//exit();
					Model_Ticket::filterByDate($_POST['from'], $_POST['to']);
				}
				//calling the unresolved ticket method
				$this->f3->set('unresolved_tickets', Bridge::nb_unresolved_tickets());
				//calling the number of ticket method
				$this->f3->set('all_tickets', Bridge::nb_all_tickets());
				
				// Modify "Main" View Area
				$this->f3->set('main','views/ticket/viewTicket.html');
				
				$this->f3->set('output',Model_Ticket::getTickets($_GET['q'], $_GET['sort'], $_GET['order'],$_GET['s'] ,$_GET['p'], null, null, $canAdmin ));
				
				//Modal Variables
				$this->f3->set('priorities',Model_Ticket::getPriority());
				$this->f3->set('status',Model_Ticket::getStatus());
				
				$this->f3->set('populate', '<script type="text/javascript"> tracking(); </script>');
				$this->f3->set('filtering', '<script type="text/javascript"> filter(); </script>');
				
				$this->f3->set('canAdmin',$canAdmin);
				$this->f3->set('editTicket','views/ticket/editTicket.html');
				//$this->f3->set('username', $this->mybbi->user['username']);
				
				// Set Page Name
				$this->config('page_name','View Tickets');
				
				// Output Layout
				echo View::instance()->render('views/layout.html');
			}
		}
		
		public function submitLogHours()
		{	
			parent::index();
			
			//calling the  js that is responsible for adding another ticket at the same page
			$this->f3->set('anotherHour', '<script type="text/javascript"> addAnotherHour('.json_encode(Model_Ticket::getResearchSites()).'); </script>');
			//calling the  js that is responsible for delete an already added ticket from a page by clicking x
			$this->f3->set('deleteHour', '<script type="text/javascript"> deleteChild(); </script>');
			//calling the cancel submit
			$this->f3->set('cancel', '<script type="text/javascript"> cancelsubmit(); </script>');
			
			if(isset($_POST['log_hour']))
			{
				for ($i=0; $i < count($_POST['desc']); $i++) 
				{
					Model_Ticket::storeTimeTrack($_POST['loginDate'][$i], $_POST['loginTime'][$i], $_POST['logoutDate'][$i], $_POST['logoutTime'][$i], $_POST['desc'][$i], $_POST['site_'.$i]);
				}
				$this->redirect('/log-hours/report', 'Your changes has been successfully saved...');
			}
			
			//username
			$this->f3->set("username", $this->mybb->user['username']);
			
			$this->f3->set('main','views/ticket/submitHour.html');
			
			// Set Page Name
			$this->config('page_name','Submit a Log Hour');
			
			// Output Layout
			echo View::instance()->render('views/layout.html');
			
			
		}
		
		public function viewLogHoursReport()
		{	
			parent::index();
			
			$this->f3->set('main','views/ticket/viewHoursReport.html');
			
			//bring the table from the database
			$this->f3->set('output', Model_Ticket::filterLogHourByDate(null,null,0));
			$this->f3->set('fromDate', date('m/01/Y'));
			$this->f3->set('callTime', '<script type="text/javascript"> callTime(); </script>');
			
			if(isset($_POST["searchByDate"]))
			{
				$from = $_POST['from'];
				$to =  $_POST['to'];
				$action = 1; //filtering
				$startMonth = strtotime(date('m/01/Y'));
				//assigning the actual dates
				$this->f3->set('toDate', $to);
				$this->f3->set('fromDate', $from);
				//converting the actual dates
				$to = strtotime($to);
				$from = strtotime($from);
				//checking the empty fields 
				if($to == "")
				{
					$this->f3->set('toDate', date('m/d/Y'));
					$to = strtotime(date('m/d/Y'));
				}
				if($from == "")
				{
					$this->f3->set('fromDate', date('m/01/Y'));
					$from = strtotime(date('m/01/Y'));
				}
				
				
				$this->f3->set('output', Model_Ticket::filterLogHourByDate($from, $to, $action));
			}
			// Set Page Name
			$this->config('page_name','View Hours Report');
			
			// Output Layout
			echo View::instance()->render('views/layout.html');
		}
		
		public function editLogHours($ticket_id)
		{	
			//bring up all the tickets id
			$tickets = Model_Ticket::getAllTicket();
			
			if(in_array($ticket_id, $tickets))
			{
				parent::index();
			
				$this->f3->set('main','views/ticket/editLogHour.html');
				
				//calling initiate form properties
				$this->f3->set('initiate', '<script type="text/javascript"> initiateFormProperties(); </script>');

				//bring up the ticket info by passing the id
				$this->f3->set('getTicketByID', Model_Ticket::getTicketByID($ticket_id));
				//bring up the research sites checked based on the ticket id
				$this->f3->set('options', Model_Ticket::getResearchSitesByTicketID($ticket_id));
				
				if(isset ($_POST["save_log_hour"]))
				{
					Model_Ticket::updateTimeTrack($ticket_id, $_POST['loginDate'], $_POST['loginTime'], $_POST['logoutDate'], $_POST['logoutTime'], $_POST['site'], $_POST['desc']);
				}
				
				if(isset ($_POST["delete_log_hour"]))
				{
					Model_Ticket::deleteTimeTrack($ticket_id);
				}
				
				// Set Page Name
				$this->config('page_name','Edit Hour Report');
				
				// Output Layout
				echo View::instance()->render('views/layout.html');
			}
			else
			{
				//NOT FOUND
				$this->f3->error(404);
			}
		}
	}
?>